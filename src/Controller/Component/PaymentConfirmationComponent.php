<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Payment Confirmation Component
 * 
 * Handles payment confirmation functionality for manual payment methods
 */
class PaymentConfirmationComponent extends Component
{
    /**
     * Confirm or reject a payment
     * 
     * @param int $paymentHistoryId ID of the payment history record
     * @param string $status Confirmation status ('Confirmed' or 'Rejected')
     * @param int $adminId ID of the admin user confirming the payment
     * @param string|null $note Optional note about the confirmation
     * @return array Status and message
     */
    public function updatePaymentStatus($paymentHistoryId, $status, $adminId, $note = null)
    {
        // Validate confirmation status
        if ($status !== 'Confirmed' && $status !== 'Rejected') {
            return [
                'status' => false,
                'message' => __('Invalid confirmation status')
            ];
        }

        // Get payment history record
        $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');

        try {
            // return [
            //     'status' => true, 
            //     'message' => __('Payment ' . strtolower($status) . ' successfully')
            // ];
            $paymentHistory = $membershipPaymentHistoryTable->get($paymentHistoryId);

            // Update payment history record
            $paymentHistory->payment_confirmation_status = $status;
            $paymentHistory->confirmed_by = $adminId;
            $paymentHistory->confirmed_date = date('Y-m-d H:i:s');
            $paymentHistory->confirmation_note = $note;

            if ($membershipPaymentHistoryTable->save($paymentHistory)) {
                // If payment was rejected, update the membership payment record to subtract the amount
                if ($status === 'Rejected') {
                    $this->_handleRejectedPayment($paymentHistory);
                }

                return [
                    'status' => true,
                    'message' => __('Payment ' . strtolower($status) . ' successfully')
                ];
            } else {
                return [
                    'status' => false,
                    'message' => __('Failed to update payment confirmation status')
                ];
            }
        } catch (\Exception $e) {

            dd($e);
            return [
                'status' => false,
                'message' => $e->getMessage(),
                // 'message' => __('Payment not found or error occurred')
            ];
        }
    }

    /**
     * Handle rejected payment by updating membership payment record
     * 
     * @param object $paymentHistory Payment history record
     * @return bool Success status
     */
    private function _handleRejectedPayment($paymentHistory)
    {
        $membershipPaymentTable = TableRegistry::get('MembershipPayment');

        try {
            $membershipPayment = $membershipPaymentTable->get($paymentHistory->mp_id);

            // Subtract the rejected payment amount
            $membershipPayment->paid_amount = $membershipPayment->paid_amount - $paymentHistory->amount;

            // Update payment status
            if ($membershipPayment->paid_amount <= 0) {
                $membershipPayment->payment_status = 'Unpaid';
            } else if ($membershipPayment->paid_amount < $membershipPayment->membership_amount) {
                $membershipPayment->payment_status = 'Partially Paid';
            }

            return $membershipPaymentTable->save($membershipPayment) ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get pending payments that need confirmation
     * 
     * @return array List of pending payments
     */
    public function getPendingPayments($branchId = null)
    {
        $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');

        // Define the association at runtime
        $membershipPaymentHistoryTable->belongsTo('MembershipPayment', [
            'foreignKey' => 'mp_id',
            'joinType' => 'INNER'
        ]);
        // Define la asociación entre MembershipPayment y Membership
        $membershipPaymentHistoryTable->MembershipPayment->belongsTo('Membership', [
            'foreignKey' => 'membership_id',
            'joinType' => 'INNER'
        ]);

        $query = $membershipPaymentHistoryTable->find()
            ->where([
                'payment_confirmation_status' => 'Pending',
                'OR' => [
                    ['payment_method' => 'Efectivo'],
                    ['payment_method' => 'Transferencia']
                ]
            ])
            ->contain(['MembershipPayment' => ['Membership']]);

        // Aplicar filtro de sucursal si está especificado
        if ($branchId) {
            $query->where(['Membership.branch_id' => $branchId]);
        }

        $pendingPayments = $query->order(['MembershipPaymentHistory.paid_by_date' => 'DESC'])
            ->toArray();
        return $pendingPayments;
    }
    /**
 * Get payment history with confirmation details
 * 
 * @param int|null $branchId Optional branch ID to filter by
 * @return array List of payment history
 */
public function getPaymentHistory($branchId = null)
{
    $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');
    
    // Define the association at runtime
    $membershipPaymentHistoryTable->belongsTo('MembershipPayment', [
        'foreignKey' => 'mp_id',
        'joinType' => 'INNER'
    ]);
    
    // Define la asociación entre MembershipPayment y Membership
    $membershipPaymentHistoryTable->MembershipPayment->belongsTo('Membership', [
        'foreignKey' => 'membership_id',
        'joinType' => 'INNER'
    ]);
    
    // Define la asociación para el usuario que creó el pago
    $membershipPaymentHistoryTable->belongsTo('Creator', [
        'className' => 'GymMember',
        'foreignKey' => 'created_by',
        'joinType' => 'LEFT'
    ]);
    
    // Define la asociación para el usuario que confirmó el pago
    $membershipPaymentHistoryTable->belongsTo('Confirmer', [
        'className' => 'GymMember',
        'foreignKey' => 'confirmed_by',
        'joinType' => 'LEFT'
    ]);
    
    $query = $membershipPaymentHistoryTable->find()
        ->contain([
            'MembershipPayment' => ['Membership', 'GymMember'],
            'Creator' => function ($q) {
                return $q->select(['id', 'first_name', 'last_name', 'email', 'mobile']);
            },
            'Confirmer' => function ($q) {
                return $q->select(['id', 'first_name', 'last_name', 'email', 'mobile']);
            }
        ])
        // No mostrar pagos pendientes en el historial
        ->where([
            'OR' => [
                ['payment_confirmation_status' => 'Confirmed'],
                ['payment_confirmation_status' => 'Rejected'],
                ['payment_confirmation_status IS NULL']
            ]
        ]);
    
    // Aplicar filtro de sucursal si está especificado
    if ($branchId) {
        $query->where(['Membership.branch_id' => $branchId]);
    }
    
    $paymentHistory = $query->order(['MembershipPaymentHistory.paid_by_date' => 'DESC'])
        ->toArray();

    return $paymentHistory;
}
}
