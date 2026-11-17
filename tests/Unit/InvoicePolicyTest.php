<?php

namespace Tests\Unit;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\User;
use App\Policies\InvoicePolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InvoicePolicyTest extends TestCase
{

    private InvoicePolicy $policy;

    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new InvoicePolicy();
        $this->invoice = new Invoice(['status' => InvoiceStatus::Issued]);
    }

    private function userWithRole(UserRole $role): User
    {
        return new User(['role' => $role]);
    }

    /**
     * @return array<string, array{UserRole, bool}>
     */
    public static function rolesThatCanViewAndCreate(): array
    {
        return [
            'receptionist can' => [UserRole::Receptionist, true],
            'housekeeping cannot' => [UserRole::Housekeeping, false],
        ];
    }

    public function test_before_allows_manager_every_abilit(): void 
    {
        $manager = $this->userWithRole(UserRole::Manager);

        $this->assertTrue($this->policy->before($manager, 'viewAny'));
        $this->assertTrue($this->policy->before($manager, 'updateStatus'));
    }

    #[DataProvider('rolesThatCanViewAndCreate')]
    public function test_view_any(UserRole $role, bool $expected): void
    {
        $this->assertSame($expected, $this->policy->viewAny($this->userWithRole($role)));
    }

        #[DataProvider('rolesThatCanViewAndCreate')]
    public function test_view(UserRole $role, bool $expected): void
    {
        $this->assertSame($expected, $this->policy->view($this->userWithRole($role), $this->invoice));
    }

    #[DataProvider('rolesThatCanViewAndCreate')]
    public function test_create(UserRole $role, bool $expected): void
    {
        $this->assertSame($expected, $this->policy->create($this->userWithRole($role)));
    }

    public function test_receptionist_change_the_invoice_status_to_paid(): void
    {
        $respone = $this->policy->updateStatus($this->userWithRole(UserRole::Receptionist), $this->invoice, InvoiceStatus::Paid);

        $this->assertTrue($respone->allowed());
    }

    public function test_a_receptionist_cannot_void_an_invoice(): void
    {
        $response = $this->policy->updateStatus(
            $this->userWithRole(UserRole::Receptionist),
            $this->invoice,
            InvoiceStatus::Void,
        );

        $this->assertTrue($response->denied());
        $this->assertSame('Only a manager can void an invoice.', $response->message());
    }

    public function test_housekeeping_cannot_change_invoice_status(): void
    {
        $response = $this->policy->updateStatus(
            $this->userWithRole(UserRole::Housekeeping),
            $this->invoice,
            InvoiceStatus::Paid,
        );

        $this->assertTrue($response->denied());
        $this->assertSame('You cannot change invoice status.', $response->message());
    }

}
