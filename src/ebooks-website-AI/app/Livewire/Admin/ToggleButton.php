<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class ToggleButton extends Component
{
    public Model $model;
    public string $field;
    public bool $active;

    public function mount(Model $model, string $field)
    {
        $this->model = $model;
        $this->field = $field;
        $this->active = (bool) $this->model->getAttribute($this->field);
    }

    public function updatedActive(bool $value): void
    {
        try {
            // Update the model in the database
            $this->model->setAttribute($this->field, $value)->save();

            // Dispatch status updated event
            $this->dispatch('statusUpdated', [
                'success' => true,
                'message' => 'Data saved successfully'
            ]);
        } catch (\Exception $e) {
            // Revert the local state if save fails
            $this->active = !$value;

            // Dispatch error event
            $this->dispatch('statusUpdated', [
                'success' => false,
                'message' => 'Failed to save: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.toggle-button');
    }
}
