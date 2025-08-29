<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Empresa $company;
    public string $search = '';
    public int $perPage = 10;

    public bool $showConfirmModal = false;
    public ?User $customerToDelete = null;

    public function mount()
    {
        $this->company = Auth::user()->empresa;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function openConfirmModal(int $customerId)
    {
        $this->customerToDelete = User::findOrFail($customerId);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->customerToDelete = null;
    }

    public function deleteCustomer()
    {
        if (!$this->customerToDelete) {
            return;
        }

        if (!$this->customerToDelete->cliente || !$this->customerToDelete->cliente->empresas->contains($this->company->id)) {
             $this->dispatch('alert', ['type' => 'error', 'message' => 'Acción no autorizada. Este cliente no pertenece a tu empresa.']);
             $this->closeConfirmModal();
             return;
        }

        $this->customerToDelete->delete();
        $this->closeConfirmModal();

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Cliente eliminado correctamente.']);
    }

    public function render()
    {
        if (!$this->company) {
            return view('livewire.customer-list-no-company');
        }

        $customersQuery = User::query()
            ->role('cliente')
            ->whereHas('cliente.empresas', function ($query) {
                $query->where('empresas.id', $this->company->id);
            })
            ->with('cliente')
            ->when($this->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                      ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })
            ->latest();

        $customers = $customersQuery->paginate($this->perPage);

        if ($customers->isEmpty() && trim($this->search)) {
            $this->dispatch('alert', ['type' => 'info', 'message' => "No se encontraron clientes para '{$this->search}'."]);
        }

        return view('livewire.customer-list', [
            'customers' => $customers,
        ]);
    }
}