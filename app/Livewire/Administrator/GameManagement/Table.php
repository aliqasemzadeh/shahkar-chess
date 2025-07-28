<?php

namespace App\Livewire\Administrator\GameManagement;

use App\Models\Chess\Game;
use Carbon\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Table extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'administrator.game-management.game.table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: $this->tableName."-".date("Y-m-d-H-i-s"))
                ->striped()
                ->type(\PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('create-game')
                ->can(auth()->user()->can('administrator_game_create'))
                ->slot(__('jetadmin.create_game'))
                ->class('btn-indigo btn-default')
                ->dispatch('modal-show', ['name' => 'administrator.game-management.create.modal']),
        ];
    }

    public function datasource(): \Illuminate\Database\Eloquent\Builder
    {
        return Game::query()->with(['whitePlayer', 'blackPlayer']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('white_player_name', fn ($model) => $model->whitePlayer->name ?? '')
            ->add('black_player_name', fn ($model) => $model->blackPlayer->name ?? '')
            ->add('start_at_formatted', fn ($model) => $model->start_at ? Carbon::parse($model->start_at)->format('Y-m-d H:i') : '-')
            ->add('end_at_formatted', fn ($model) => $model->end_at ? Carbon::parse($model->end_at)->format('Y-m-d H:i') : '-')
            ->add('white_player_code')
            ->add('black_player_code')
            ->add('created_at_formatted', fn ($model) => Carbon::parse($model->created_at)->format('Y-m-d H:i'))
            ->add('updated_at_formatted', fn ($model) => Carbon::parse($model->updated_at)->format('Y-m-d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make(__('jetadmin.id'), 'id')
                ->sortable()
                ->searchable(),
            Column::make(__('jetadmin.white_player'), 'white_player_name')
                ->sortable()
                ->searchable(),
            Column::make(__('jetadmin.black_player'), 'black_player_name')
                ->sortable()
                ->searchable(),
            Column::make(__('jetadmin.start_at'), 'start_at_formatted', 'start_at')
                ->sortable(),
            Column::make(__('jetadmin.end_at'), 'end_at_formatted', 'end_at')
                ->sortable(),
            Column::make(__('jetadmin.white_player_code'), 'white_player_code')
                ->sortable()
                ->searchable(),
            Column::make(__('jetadmin.black_player_code'), 'black_player_code')
                ->sortable()
                ->searchable(),
            Column::make(__('jetadmin.created_at'), 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::action(__('jetadmin.action'))
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datetimepicker('start_at'),
            Filter::datetimepicker('end_at'),
            Filter::datetimepicker('created_at'),
            Filter::datetimepicker('updated_at'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        Game::findOrFail($id)->delete();
        $this->dispatch('pg:eventRefresh-'.$this->tableName);
        
        // Show success message
        session()->flash('success', __('jetadmin.game_deleted_successfully'));
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot(__('jetadmin.edit'))
                ->id()
                ->can(auth()->user()->can('administrator_game_edit'))
                ->class('btn-blue btn-xs')
                ->dispatch("administrator.game-management.game.edit.assign-data", [$row->id]),
            Button::add('delete')
                ->slot(__('jetadmin.delete'))
                ->id()
                ->can(auth()->user()->can('administrator_game_delete'))
                ->class('btn-red btn-xs')
                ->confirm(__('jetadmin.are_you_sure'))
                ->dispatch('delete', ['id' => $row->id])
        ];
    }
}
