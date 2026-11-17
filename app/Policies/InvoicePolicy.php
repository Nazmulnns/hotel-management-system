<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Managers can do everything with invoices.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isManager()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::Receptionist);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(UserRole::Receptionist);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::Receptionist);
    }

    public function updateStatus(User $user, Invoice $invoice, InvoiceStatus $newStatus): Response
    {
        if ($newStatus === InvoiceStatus::Void) {
            return Response::deny('Only a manager can void an invoice.');
        }

        return $user->hasRole(UserRole::Receptionist)
            ? Response::allow()
            : Response::deny('You cannot change invoice status.');
    }
}
