<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\KidsWear\Admin\ResourceCatalog;
use App\Modules\KidsWear\Support\ModuleManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(string $resource): View
    {
        $definition = $this->definition($resource, 'view');
        /** @var class-string<Model> $model */
        $model = $definition['model'];
        $query = $model::query()->with($definition['with']);
        if ($search = trim((string) request('q'))) {
            $query->where(function ($builder) use ($definition, $search): void {
                foreach (array_unique([...$definition['columns'], $definition['title'], 'name', 'number', 'title', 'code', 'sku']) as $column) {
                    if (! str_contains($column, '.') && \Illuminate\Support\Facades\Schema::hasColumn($builder->getModel()->getTable(), $column)) {
                        $builder->orWhere($column, 'like', '%'.$search.'%');
                    }
                }
            });
        }

        return view('admin.resource.index', [
            'definition' => $definition,
            'records' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(string $resource): View
    {
        $definition = $this->definition($resource, 'manage');

        return view('admin.resource.form', [
            'definition' => $definition,
            'record' => null,
            'options' => $this->options($definition),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource, 'manage');
        $data = $this->validatePayload($request, $definition);
        /** @var class-string<Model> $model */
        $model = $definition['model'];

        try {
            $record = $model::query()->create($data);
        } catch (\Throwable $exception) {
            return back()->withInput()->withErrors(['form' => $exception->getMessage()]);
        }

        return redirect()->route('admin.resources.show', [$resource, $record->getKey()])->with('status', $definition['label'].' saved.');
    }

    public function show(string $resource, int $id): View
    {
        $definition = $this->definition($resource, 'view');
        /** @var class-string<Model> $model */
        $model = $definition['model'];

        return view('admin.resource.show', [
            'definition' => $definition,
            'record' => $model::query()->with($definition['with'])->findOrFail($id),
        ]);
    }

    public function edit(string $resource, int $id): View
    {
        $definition = $this->definition($resource, 'manage');
        /** @var class-string<Model> $model */
        $model = $definition['model'];

        return view('admin.resource.form', [
            'definition' => $definition,
            'record' => $model::query()->findOrFail($id),
            'options' => $this->options($definition),
        ]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $definition = $this->definition($resource, 'manage');
        /** @var class-string<Model> $model */
        $model = $definition['model'];
        $record = $model::query()->findOrFail($id);
        $record->update($this->validatePayload($request, $definition));

        return redirect()->route('admin.resources.show', [$resource, $record->getKey()])->with('status', 'Updated.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $definition = $this->definition($resource, 'manage');
        /** @var class-string<Model> $model */
        $model = $definition['model'];
        $model::query()->findOrFail($id)->delete();

        return redirect()->route('admin.resources.index', $resource)->with('status', 'Deleted.');
    }

    /** @return array<string, mixed> */
    private function definition(string $resource, string $ability): array
    {
        $definition = ResourceCatalog::find($resource);
        abort_unless(ModuleManager::enabled($definition['module']), 404);
        abort_unless(auth()->user()?->can($definition['module'].'.'.$ability), 403);

        return $definition;
    }

    /** @param  array<string, mixed>  $definition
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, array $definition): array
    {
        $rules = [];
        foreach ($definition['fields'] as $field) {
            $rule = [$field['required'] ?? false ? 'required' : 'nullable'];
            $rule[] = match ($field['type']) {
                'number' => 'numeric',
                'date' => 'date',
                'checkbox' => 'boolean',
                default => 'string',
            };
            $rules[$field['name']] = $rule;
        }

        $data = $request->validate($rules);
        foreach ($definition['fields'] as $field) {
            if ($field['type'] === 'checkbox') {
                $data[$field['name']] = $request->boolean($field['name']);
            }
            if (($data[$field['name']] ?? null) === '') {
                $data[$field['name']] = null;
            }
        }

        return $data;
    }

    /** @param  array<string, mixed>  $definition
     * @return array<string, array<int|string, string>>
     */
    private function options(array $definition): array
    {
        $options = [];
        foreach ($definition['fields'] as $field) {
            if ($field['type'] !== 'select') {
                continue;
            }
            if (is_array($field['options'])) {
                $options[$field['name']] = $field['options'];
                continue;
            }
            if ($field['model']) {
                $options[$field['name']] = $field['model']::query()->orderBy($field['option_label'])->pluck($field['option_label'], 'id')->all();
            }
        }

        return $options;
    }
}
