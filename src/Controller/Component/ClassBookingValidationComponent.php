<?php
// namespace App\Controller\Component;

// use Cake\Controller\Component;
// use Cake\ORM\TableRegistry;
// use Cake\I18n\Time;

// /**
//  * Class Booking Validation Component
//  * 
//  * Handles validation rules for class bookings
//  */
// class ClassBookingValidationComponent extends Component
// {
//     /**
//      * Other components used by this component
//      *
//      * @var array
//      */
//     public $components = ['GYMFunction'];

//     /**
//      * Check if a member has a valid membership
//      * 
//      * @param int $memberId The member ID to check
//      * @return array ['status' => bool, 'message' => string, 'membership' => object|null]
//      */
//     public function hasValidMembership($memberId)
//     {
//         $membershipHistoryTable = TableRegistry::get('membership_payment');
//         $activeMembership = $membershipHistoryTable->find()
//         ->where([
//             'member_id' => $memberId,
//             'payment_status' => 0,
//             'end_date >=' => date('Y-m-d'),
//             'start_date <=' => date('Y-m-d'),
//         ])
//         ->order(['member_id' => 'DESC'])
//         ->first();


//         // $activeMembership = $membershipHistoryTable->find()
//         //     ->where([
//         //         'member_id' => $memberId,
//         //         'membership_valid_to >' => date('Y-m-d'),
//         //         'membership_valid_from <=' => date('Y-m-d')
//         //     ])
//         //     ->order(['id' => 'DESC'])
//         //     ->first();

//         if (!$activeMembership) {
//             return [
//                 'status' => true,
//                 'message' => __('No tienes una membresía activa'),
//                 'membership' => null
//             ];
//         }

//         return [
//             'status' => true,
//             'message' => '',
//             'membership' => $activeMembership
//         ];
//     }

//     /**
//      * Check if a member has exceeded their class limit for a LIMITED membership
//      * 
//      * @param int $memberId The member ID to check
//      * @param object $membership The membership object
//      * @param int $branchId The branch ID of the class
//      * @param string $bookingDate The booking date (YYYY-MM-DD)
//      * @return array ['status' => bool, 'message' => string, 'remaining' => int]
//      */
//     public function checkClassLimit($memberId, $membership, $branchId, $bookingDate)
//     {
//         // If membership is UNLIMITED, no need to check limits
//         if ($membership->membership_class_limit === 'UNLIMITED') {
//             return [
//                 'status' => true,
//                 'message' => '',
//                 'remaining' => PHP_INT_MAX
//             ];
//         }

//         // For LIMITED memberships, check if the class is in the membership branch
//         if ($membership->branch_id == $branchId) {
//             // Check if membership_id is NULL
//             if (empty($membership->membership_id)) {
//                 return [
//                     'status' => true,
//                     'message' => '',
//                     'remaining' => 0
//                 ];
//             }

//             // Get membership details to find class limit
//             $membershipTable = TableRegistry::get('membership');
//             $membershipDetails = $membershipTable->get($membership->membership_id);

//             // If no limit_days is set, default to unlimited
//             if (empty($membershipDetails->limit_days)) {
//                 return [
//                     'status' => true,
//                     'message' => '',
//                     'remaining' => PHP_INT_MAX
//                 ];
//             }

//             $limitDays = (int)$membershipDetails->limit_days;
//             $limitation = $membershipDetails->limitation ?: 'per_month'; // Default to per_month if not set

//             // Calculate date range based on limitation type
//             $startDate = '';
//             $endDate = '';
//             $periodText = '';

//             if ($limitation === 'per_week') {
//                 // Get the week start (Monday) and end (Sunday) dates
//                 $dayOfWeek = date('N', strtotime($bookingDate));
//                 $startDate = date('Y-m-d', strtotime($bookingDate . " -" . ($dayOfWeek - 1) . " days"));
//                 $endDate = date('Y-m-d', strtotime($startDate . " +6 days"));
//                 $periodText = __('esta semana');
//             } else { // per_month
//                 // Get the month start and end dates
//                 $startDate = date('Y-m-01', strtotime($bookingDate));
//                 $endDate = date('Y-m-t', strtotime($bookingDate));
//                 $periodText = __('este mes');
//             }

//             // Get current period's bookings for this member in this branch
//             $classBookingTable = TableRegistry::get('class_booking');

//             $currentBookings = $classBookingTable->find()
//                 ->where([
//                     'member_id' => $memberId,
//                     'booking_date >=' => $startDate,
//                     'booking_date <=' => $endDate,
//                     'status !=' => 'Cancelled'
//                 ])
//                 ->matching('ClassSchedule', function ($q) use ($branchId) {
//                     return $q->where(['ClassSchedule.branch_id' => $branchId]);
//                 })
//                 ->count();

//             $remaining = $limitDays - $currentBookings;

//             if ($remaining <= 0) {
//                 return [
//                     'status' => false,
//                     'message' => __("Has alcanzado el límite de clases para tu membresía en {0}", $periodText),
//                     'remaining' => 0
//                 ];
//             }

//             return [
//                 'status' => true,
//                 'message' => '',
//                 'remaining' => $remaining
//             ];
//         }

//         // If class is not in membership branch, it will be checked by credits
//         return [
//             'status' => true,
//             'message' => '',
//             'remaining' => 0
//         ];
//     }

//     /**
//      * Check if a member has enough credits for booking a class in another branch
//      * 
//      * @param int $memberId The member ID to check
//      * @param object $membership The membership object
//      * @param int $branchId The branch ID of the class
//      * @return array ['status' => bool, 'message' => string, 'credits' => int]
//      */
//     public function checkBranchCredits($memberId, $membership, $branchId)
//     {
//         // If class is in the same branch as membership, no credits needed
//         if ($membership->branch_id == $branchId) {
//             return [
//                 'status' => true,
//                 'message' => '',
//                 'credits' => 0
//             ];
//         }

//         // Check if member has credits for this branch
//         $membershipCreditsTable = TableRegistry::get('membership_credits');

//         $credits = $membershipCreditsTable->find()
//             ->where([
//                 'member_id' => $memberId,
//                 'branch_id' => $branchId,
//                 'expiry_date >=' => date('Y-m-d')
//             ])
//             ->order(['id' => 'DESC'])
//             ->first();

//         if (!$credits || $credits->remaining_credits <= 0) {
//             return [
//                 'status' => false,
//                 'message' => __('No tienes créditos disponibles para reservar en esta sede'),
//                 'credits' => 0
//             ];
//         }

//         return [
//             'status' => true,
//             'message' => '',
//             'credits' => $credits->remaining_credits
//         ];
//     }
// //     public function checkBranchCredits($memberId, $membership, $branchId)
// // {
// //     // Si la clase está en la misma sucursal que la membresía, no se necesitan créditos
// //     if ($membership->branch_id == $branchId) {
// //         return [
// //             'status' => true,
// //             'message' => '',
// //             'credits' => 0
// //         ];
// //     }

// //     // Utilizar una conexión directa para consulta SQL para evitar problemas con el ORM
// //     $connection = \Cake\Datasource\ConnectionManager::get('default');

// //     // Consulta SQL que relaciona las tablas correctamente
// //     $query = "SELECT mc.credits as remaining_credits, mc.expiry_date 
// //               FROM membership_credits mc
// //               JOIN membership m ON mc.membership_id = m.id
// //               JOIN membership_history mh ON mh.selected_membership = m.id
// //               WHERE mh.member_id = :member_id
// //               AND m.branch_id = :branch_id
// //               AND mc.expiry_date >= :today
// //               ORDER BY mc.id DESC
// //               LIMIT 1";

// //     $params = [
// //         'member_id' => $memberId,
// //         'branch_id' => $branchId,
// //         'today' => date('Y-m-d')
// //     ];

// //     // Ejecutar la consulta
// //     $result = $connection->execute($query, $params)->fetch('assoc');

// //     if (!$result || empty($result['remaining_credits']) || $result['remaining_credits'] <= 0) {
// //         return [
// //             'status' => false,
// //             'message' => __('No tienes créditos disponibles para reservar en esta sede'),
// //             'credits' => 0
// //         ];
// //     }

// //     return [
// //         'status' => true,
// //         'message' => '',
// //         'credits' => $result['remaining_credits']
// //     ];
// // }

//     /**
//      * Validate all booking conditions for a member
//      * 
//      * @param int $memberId The member ID
//      * @param int $classId The class ID
//      * @param string $bookingDate The booking date (YYYY-MM-DD)
//      * @return array ['status' => bool, 'message' => string]
//      */
//     public function validateBooking($memberId, $classId, $bookingDate)
//     {
//         // 1. Check if member has valid membership
//         $membershipCheck = $this->hasValidMembership($memberId);
//         if (!$membershipCheck['status']) {
//             return $membershipCheck;
//         }

//         // Get class details to check branch
//         $classScheduleTable = TableRegistry::get('class_schedule');
//         $class = $classScheduleTable->get($classId);
//         $branchId = $class->branch_id;

//         // 2. Check class limits for LIMITED memberships
//         $limitCheck = $this->checkClassLimit(
//             $memberId, 
//             $membershipCheck['membership'], 
//             $branchId, 
//             $bookingDate
//         );

//         if (!$limitCheck['status']) {
//             return $limitCheck;
//         }

//         // 3. If class is in another branch, check credits
//         if ($membershipCheck['membership']->branch_id != $branchId) {
//             $creditCheck = $this->checkBranchCredits(
//                 $memberId, 
//                 $membershipCheck['membership'], 
//                 $branchId
//             );

//             if (!$creditCheck['status']) {
//                 return $creditCheck;
//             }
//         }

//         return [
//             'status' => true,
//             'message' => ''
//         ];
//     }

//     /**
//      * Deduct a credit if booking is for another branch
//      * 
//      * @param int $memberId The member ID
//      * @param int $branchId The branch ID of the class
//      * @return bool Success status
//      */
//     public function deductCredit($memberId, $branchId)
//     {
//         $membershipHistoryTable = TableRegistry::get('membership_history');
//         $activeMembership = $membershipHistoryTable->find()
//             ->where([
//                 'member_id' => $memberId,
//                 'membership_valid_to >=' => date('Y-m-d'),
//                 'membership_valid_from <=' => date('Y-m-d')
//             ])
//             ->order(['id' => 'DESC'])
//             ->first();

//         // If class is in the same branch as membership, no credits needed
//         if ($activeMembership && $activeMembership->branch_id == $branchId) {
//             return true;
//         }

//         // Deduct a credit
//         $membershipCreditsTable = TableRegistry::get('membership_credits');
//         $credits = $membershipCreditsTable->find()
//             ->where([
//                 'member_id' => $memberId,
//                 'branch_id' => $branchId,
//                 'expiry_date >=' => date('Y-m-d'),
//                 'remaining_credits >' => 0
//             ])
//             ->order(['id' => 'DESC'])
//             ->first();

//         if ($credits) {
//             $credits->remaining_credits -= 1;
//             $credits->last_used_date = date('Y-m-d');
//             return $membershipCreditsTable->save($credits) ? true : false;
//         }

//         return false;
//     }

//     /**
//      * Get membership class limit status for a member
//      * 
//      * @param int $memberId The member ID to check
//      * @param string $bookingDate The booking date (YYYY-MM-DD) to check against
//      * @return array ['limit_type' => string, 'days_used' => int, 'days_total' => int, 'period' => string]
//      */
//     public function getMembershipLimitStatus($memberId, $bookingDate = null)
//     {
//         $bookingDate = $bookingDate ?: date('Y-m-d');
//         $today = date('Y-m-d');

//         // Get active membership
//         $membershipCheck = $this->hasValidMembership($memberId);
//         if (!$membershipCheck['status']) {
//             return [
//                 'limit_type' => 'NONE',
//                 'days_used' => 0,
//                 'days_total' => 0,
//                 'period' => '',
//                 'message' => $membershipCheck['message']
//             ];
//         }

//         $membership = $membershipCheck['membership'];

//         // If membership is UNLIMITED, return unlimited status
//         if ($membership->membership_class_limit === 'Unlimited') {
//             return [
//                 'limit_type' => 'UNLIMITED',
//                 'days_used' => 0,
//                 'days_total' => 0,
//                 'period' => '',
//                 'message' => __('Membresía ilimitada')
//             ];
//         }

//         // Check if membership_id is NULL
//         if (empty($membership->id)) {
//             return [
//                 'limit_type' => 'LIMITED',
//                 'days_used' => 0,
//                 'days_total' => 0,
//                 'period' => '',
//                 'message' => __('Información de membresía no disponible')
//             ];
//         }

//         // Get membership details
//         $membershipTable = TableRegistry::get('membership');
//         $membershipDetails = $membershipTable->get($membership->selected_membership);

//         // If no limit_days is set, return unlimited
//         if (empty($membershipDetails->limit_days)) {
//             return [
//                 'limit_type' => 'UNLIMITED',
//                 'days_used' => 0,
//                 'days_total' => 0,
//                 'period' => '',
//                 'message' => __('Sin límite de clases')
//             ];
//         }

//         $limitDays = (int)$membershipDetails->limit_days;
//         $limitation = $membershipDetails->limitation ?: 'per_month';

//         // Calculate date range based on limitation type
//         $startDate = '';
//         $endDate = '';
//         $periodText = '';

//         if ($limitation === 'per_week') {
//             // Get the week start (Monday) and end (Sunday) dates
//             $dayOfWeek = date('N', strtotime($bookingDate));
//             $startDate = date('Y-m-d', strtotime($bookingDate . " -" . ($dayOfWeek - 1) . " days"));
//             $endDate = date('Y-m-d', strtotime($startDate . " +6 days"));
//             $periodText = __('esta semana');
//         } else { // per_month
//             // Get the month start and end dates
//             $startDate = date('Y-m-01', strtotime($bookingDate));
//             $endDate = date('Y-m-t', strtotime($bookingDate));
//             $periodText = __('este mes');
//         }

//         // Get current period's bookings for this member in this branch
//         $classBookingTable = TableRegistry::get('class_booking');

//         // Count only past bookings as "used"
//         $usedBookings = $classBookingTable->find()
//             ->where([
//                 'member_id' => $memberId,
//                 'booking_date >=' => $startDate,
//                 'booking_date <=' => $endDate,
//                 'booking_date <' => $today, // Only count bookings up to today
//                 'status !=' => 'Cancelled'
//             ])
//             ->matching('ClassSchedule', function ($q) use ($membership) {
//                 return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
//             })
//             ->count();

//         // Count all bookings (past and future) for the total
//         $totalBookings = $classBookingTable->find()
//             ->where([
//                 'member_id' => $memberId,
//                 'booking_date >=' => $startDate,
//                 'booking_date <=' => $endDate,
//                 'status !=' => 'Cancelled'
//             ])
//             ->matching('ClassSchedule', function ($q) use ($membership) {
//                 return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
//             })
//             ->count();

//         $remainingBookings = $limitDays - $totalBookings;

//         return [
//             'limit_type' => 'LIMITED',
//             'days_used' => $usedBookings,
//             'days_total' => $limitDays,
//             'days_booked' => $totalBookings,
//             'days_remaining' => $remainingBookings,
//             'period' => $limitation,
//             'period_text' => $periodText,
//             'message' => __('Clases usadas: {0}/{1} {2} (Reservadas: {3})', $usedBookings, $limitDays, $periodText, $totalBookings)
//         ];
//     }

//     /**
//      * Get branch credits information for a member
//      * 
//      * @param int $memberId The member ID to check
//      * @return array Array of branch credits information
//      */
//     public function getBranchCredits($memberId)
//     {
//         // Get active membership
//         $membershipCheck = $this->hasValidMembership($memberId);
//         if (!$membershipCheck['status']) {
//             return [];
//         }

//         $membership = $membershipCheck['membership'];

//         // Check if branch_id is NULL
//         if (empty($membership->branch_id)) {
//             return [];
//         }

//         $membershipBranchId = $membership->branch_id;

//         // Get all branches
//         $branchTable = TableRegistry::get('gym_branch');
//         $branches = $branchTable->find('all')->toArray();

//         // Get credits for each branch
//         $membershipCreditsTable = TableRegistry::get('membership_credits');
//         $branchCredits = [];

//         foreach ($branches as $branch) {
//             // Skip member's home branch
//             if ($branch->id == $membershipBranchId) {
//                 continue;
//             }

//             $credits = $membershipCreditsTable->find()
//                 ->where([
//                     'member_id' => $memberId,
//                     'branch_id' => $branch->id,
//                 ])
//                 ->order(['id' => 'DESC'])
//                 ->first();

//             $branchCredits[] = [
//                 'branch_id' => $branch->id,
//                 'branch_name' => $branch->branch_name,
//                 'credits' => $credits ? $credits->remaining_credits : 0,
//                 'expiry_date' => $credits ? $credits->expiry_date : null
//             ];
//         }

//         return $branchCredits;
//     }
// }






































// // <?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Log\Log;

/**
 * Class Booking Validation Component
 * 
 * Handles validation rules for class bookings
 */
class ClassBookingValidationComponent extends Component
{
    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = ['GYMFunction'];

    /**
     * Check if a member has a valid membership
     * 
     * @param int $memberId The member ID to check
     * @return array ['status' => bool, 'message' => string, 'membership' => object|null]
  
     */

    public function hasValidMembership($memberId)
    {
        $membershipHistoryTable = TableRegistry::get('membership_history');
        $membershipPaymentTable = TableRegistry::get('membership_payment');
        $membershipPaymentHistoryTable = TableRegistry::get('membership_payment_history');
        // Get user's active membership and branch
          // Buscar la membresía activa en el historial
        $activeMembership = $membershipHistoryTable->find()
            ->where([
                'member_id' => $memberId,
                'membership_valid_to >=' => date('Y-m-d'),
                'membership_valid_from <=' => date('Y-m-d')
            ])
            ->order(['id' => 'DESC'])
            ->first();

        if (!$activeMembership) {
            return [
                'status' => false,
                'message' => __('No tienes una membresía activa'),
                'membership' => null
            ];
        }

        // Validar que el pago esté confirmado
        if (!empty($activeMembership->mp_id)) {
            $paymentHistory = $membershipPaymentHistoryTable->find()
                ->where(['mp_id' => $activeMembership->mp_id, 'payment_confirmation_status' => 'Confirmed'])
                ->first();

            if (!$paymentHistory) {
                return [
                    'status' => false,
                    'message' => __('No tienes una membresía activa (pago no confirmado)'),
                    'membership' => null
                ];
            }
        }
        // $activeMembership = $membershipPaymentTable->find()
        //     ->where(['member_id' => $memberId])
        //     ->order(['mp_id' => 'DESC']) // Get the latest record
        //     ->first();

        // if (!$activeMembership) {
        //     return [
        //         'status' => false,  // Cambiado a false para mantener consistencia
        //         'message' => __('No tienes una membresía activas'),
        //         'membership' => null
        //     ];
        // }

        // // Get the mp_id from the latest membership payment
        // $mpId = $activeMembership->mp_id;

        // // Check the payment confirmation status in membership_payment_history
        // $paymentHistory = $membershipPaymentHistoryTable->find()
        //     ->where(['mp_id' => $mpId, 'payment_confirmation_status' => 'Confirmed'])
        //     ->first();

        // if (!$paymentHistory) {
        //     return [
        //         'status' => false,  // Cambiado a false para mantener consistencia
        //         'message' => __('No tienes una membresía activaa'),
        //         'membership' => null
        //     ];
        // }
        //  $activeMembership = $membershipHistoryTable->find()
        //      ->where([
        //          'member_id' => $memberId,
        //          'membership_valid_to >' => date('Y-m-d'),
        //          'membership_valid_from <=' => date('Y-m-d')
        //      ])
        //      ->order(['id' => 'DESC'])
        //      ->first();

        //  if (!$activeMembership) {
        //      return [
        //          'status' => false,  // Cambiado a false para mantener consistencia
        //          'message' => __('No tienes una membresía activa'),
        //          'membership' => null
        //      ];
        //  }

        return [
            'status' => true,
            'message' => '',
            'membership' => $activeMembership
        ];
    }
    // public function hasValidMembership($memberId)
    // {
    //     $membershipHistoryTable = TableRegistry::get('membership_history');

    //     $activeMembership = $membershipHistoryTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'membership_valid_to >' => date('Y-m-d'),
    //             'membership_valid_from <=' => date('Y-m-d')
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //     if (!$activeMembership) {
    //         return [
    //             'status' => true,
    //             'message' => __('No tienes una membresía activa'),
    //             'membership' => null
    //         ];
    //     }

    //     return [
    //         'status' => true,
    //         'message' => '',
    //         'membership' => $activeMembership
    //     ];
    // }

    /**
     * Check if a member has exceeded their class limit for a LIMITED membership
     * 
     * @param int $memberId The member ID to check
     * @param object $membership The membership object
     * @param int $branchId The branch ID of the class
     * @param string $bookingDate The booking date (YYYY-MM-DD)
     * @return array ['status' => bool, 'message' => string, 'remaining' => int]
     */
    public function checkClassLimit($memberId, $membership, $branchId, $bookingDate)
    {
        // // If membership is UNLIMITED, no need to check limits
        // if ($membership->membership_class_limit === 'UNLIMITED') {
        //     return [
        //         'status' => true,
        //         'message' => '',
        //         'remaining' => PHP_INT_MAX
        //     ];
        // }
        // Verificar si $membership es nulo
        if (!$membership) {
            return [
                'status' => true,
                'message' => '',
                'remaining' => 0
            ];
        }

        // Si membership_class_limit no está definido o es nulo
        if (!isset($membership->membership_class_limit)) {
            // Obtener los detalles de la membresía
            $membershipTable = TableRegistry::get('membership');
            try {
                $membershipDetails = $membershipTable->get($membership->selected_membership);
                // Si es ilimitada según los detalles
                if (strtoupper($membershipDetails->membership_class_limit) === 'UNLIMITED') {
                    return [
                        'status' => true,
                        'message' => '',
                        'remaining' => PHP_INT_MAX
                    ];
                }
            } catch (\Exception $e) {
                Log::write('error', 'Error al obtener detalles de membresía: ' . $e->getMessage());
                return [
                    'status' => true,
                    'message' => '',
                    'remaining' => 0
                ];
            }
        } else if ($membership->membership_class_limit === 'UNLIMITED') {
            return [
                'status' => true,
                'message' => '',
                'remaining' => PHP_INT_MAX
            ];
        }

        // Verificar si branch_id está definido
        if (!isset($membership->branch_id)) {
            return [
                'status' => true,
                'message' => '',
                'remaining' => 0
            ];
        }
        // For LIMITED memberships, check if the class is in the membership branch
        if ($membership->branch_id == $branchId) {
            // Check if membership_id is NULL
            if (empty($membership->membership_id)) {
                return [
                    'status' => true,
                    'message' => '',
                    'remaining' => 0
                ];
            }

            // Get membership details to find class limit
            $membershipTable = TableRegistry::get('membership');
            $membershipDetails = $membershipTable->get($membership->membership_id);

            // If no limit_days is set, default to unlimited
            if (empty($membershipDetails->limit_days)) {
                return [
                    'status' => true,
                    'message' => '',
                    'remaining' => PHP_INT_MAX
                ];
            }

            $limitDays = (int)$membershipDetails->limit_days;
            $limitation = $membershipDetails->limitation ?: 'per_month'; // Default to per_month if not set

            // Calculate date range based on limitation type
            $startDate = '';
            $endDate = '';
            $periodText = '';

            if ($limitation === 'per_week') {
                // Get the week start (Monday) and end (Sunday) dates
                $dayOfWeek = date('N', strtotime($bookingDate));
                $startDate = date('Y-m-d', strtotime($bookingDate . " -" . ($dayOfWeek - 1) . " days"));
                $endDate = date('Y-m-d', strtotime($startDate . " +6 days"));
                $periodText = __('esta semana');
            } else { // per_month
                // Get the month start and end dates
                $startDate = date('Y-m-01', strtotime($bookingDate));
                $endDate = date('Y-m-t', strtotime($bookingDate));
                $periodText = __('este mes');
            }

            // Get current period's bookings for this member in this branch
            $classBookingTable = TableRegistry::get('class_booking');

            $currentBookings = $classBookingTable->find()
                ->where([
                    'member_id' => $memberId,
                    'booking_date >=' => $startDate,
                    'booking_date <=' => $endDate,
                    'status !=' => 'Cancelled'
                ])
                ->matching('ClassSchedule', function ($q) use ($branchId) {
                    return $q->where(['ClassSchedule.branch_id' => $branchId]);
                })
                ->count();

            $remaining = $limitDays - $currentBookings;

            if ($remaining <= 0) {
                return [
                    'status' => false,
                    'message' => __("Has alcanzado el límite de clases para tu membresía en {0}", $periodText),
                    'remaining' => 0
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'remaining' => $remaining
            ];
        }

        // If class is not in membership branch, it will be checked by credits
        return [
            'status' => true,
            'message' => '',
            'remaining' => 0
        ];
    }

    /**
     * Check if a member has enough credits for booking a class in another branch
     * 
     * @param int $memberId The member ID to check
     * @param object $membership The membership object
     * @param int $branchId The branch ID of the class
     * @return array ['status' => bool, 'message' => string, 'credits' => int]
     */
    public function checkBranchCredits($memberId, $membership, $branchId)
    {

        //Log::write('debug', 'checkBranchCredits called with memberId: ' . $membership);
        // // Verificar si la membresía es nula o no tiene branch_id
        // if (!$membership || !isset($membership->branch_id)) {
        //     return [
        //         'status' => false,
        //         'message' => __('Información de membresía no disponible'),
        //         'credits' => 0
        //     ];
        // }

        // Si la clase está en la misma sucursal que la membresía, no se necesitan créditos
        if ($membership->branch_id == $branchId) {
            return [
                'status' => true,
                'message' => '',
                'credits' => 0
            ];
        }

        // Conexión directa para ejecutar la consulta SQL
        $connection = \Cake\Datasource\ConnectionManager::get('default');

        // Consulta SQL para obtener los créditos restantes para la sucursal específica
        $query = "
        SELECT 
            gmc.credits_remaining AS remaining_credits,
            gmc.updated_at AS last_updated
        FROM 
            gym_member_credits gmc
        INNER JOIN 
            gym_member_memberships gmm ON gmm.id = gmc.gym_member_membership_id
        WHERE 
            gmm.member_id = :member_id
            AND gmc.branch_id = :branch_id
            AND gmm.status = 'active'
        LIMIT 1
    ";

        $params = [
            'member_id' => $memberId,
            'branch_id' => $branchId
        ];

        // Ejecutar la consulta
        $result = $connection->execute($query, $params)->fetch('assoc');

        // Verificar si hay créditos disponibles
        if (!$result || $result['remaining_credits'] <= 0) {
            return [
                'status' => false,
                'message' => __('No tienes créditos disponibles para reservar en esta sede'),
                'credits' => 0
            ];
        }

        // Retornar los créditos disponibles
        return [
            'status' => true,
            'message' => '',
            'credits' => $result['remaining_credits']
        ];
    }
    // public function checkBranchCredits($memberId, $membership, $branchId)
    // {
    //     // Verificar si $membership es nulo o no tiene branch_id
    // // if (!$membership || !isset($membership->branch_id)) {
    // //     return [
    // //         'status' => false,
    // //         'message' => __('Información de membresía no disponible'),
    // //         'credits' => 0
    // //     ];
    // // }

    // // Si la clase está en la misma sucursal que la membresía, no se necesitan créditos
    // if ($membership->branch_id == $branchId) {
    //     return [
    //         'status' => true,
    //         'message' => '',
    //         'credits' => 0
    //     ];
    // }
    //     // // Si la clase está en la misma sucursal que la membresía, no se necesitan créditos
    //     // if ($membership->branch_id == $branchId) {
    //     //     return [
    //     //         'status' => true,
    //     //         'message' => '',
    //     //         'credits' => 0
    //     //     ];
    //     // }

    //     // Utilizar una conexión directa para consulta SQL
    //     $connection = \Cake\Datasource\ConnectionManager::get('default');

    //     // Consulta SQL para obtener créditos a través de relaciones
    //     $query = "SELECT mc.credits as remaining_credits, mh.membership_valid_to as expiry_date
    //               FROM membership_credits mc
    //               JOIN membership m ON mc.membership_id = m.id
    //               JOIN membership_history mh ON mh.selected_membership = m.id
    //               WHERE mh.member_id = :member_id
    //               AND m.branch_id = :branch_id
    //               AND mh.membership_valid_to >= :today
    //               ORDER BY mh.id DESC
    //               LIMIT 1";

    //     $params = [
    //         'member_id' => $memberId,
    //         'branch_id' => $branchId,
    //         'today' => date('Y-m-d')
    //     ];

    //     Log::write('', 'SQL Query: ' . $query);
    //     Log::write('debug', 'Parameters: ' . json_encode($params));

    //     // Ejecutar la consulta
    //     $result = $connection->execute($query, $params)->fetch('assoc');

    //     Log::write('debug', 'Query result: ' . json_encode($result));

    //     if (!$result || $result['remaining_credits'] <= 0) {
    //         return [
    //             'status' => false,
    //             'message' => __('No tienes créditos disponibles para reservar en esta sede'),
    //             'credits' => 0
    //         ];
    //     }

    //     return [
    //         'status' => true,
    //         'message' => '',
    //         'credits' => $result['remaining_credits']
    //     ];
    // }
    // public function checkBranchCredits($memberId, $membership, $branchId)
    // {
    //     // Log::write('debug', 'Checking branch credits for member ID: ' . $memberId);
    //     // Log::write('debug', 'Checking branch membership for member ID: ' . $membership);
    //     // Log::write('debug', 'Checking branch branchId for member ID: ' . $branchId);
    //     // If class is in the same branch as membership, no credits needed
    //     if ($membership->branch_id == $branchId) {
    //         return [
    //             'status' => true,
    //             'message' => '',
    //             'credits' => 0
    //         ];
    //     }

    //     // Check if member has credits for this branch
    //     $membershipCreditsTable = TableRegistry::get('membership_credits');

    //     $credits = $membershipCreditsTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'branch_id' => $branchId,
    //             'expiry_date >=' => date('Y-m-d')
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //         Log::write('debug', 'Checking CREDITS ' . $credits);
    //     if (!$credits || $credits->remaining_credits <= 0) {
    //         return [
    //             'status' => false,
    //             'message' => __('No tienes créditos disponibles para reservar en esta sede'),
    //             'credits' => 0
    //         ];
    //     }

    //     return [
    //         'status' => true,
    //         'message' => '',
    //         'credits' => $credits->remaining_credits
    //     ];
    // }

    /**
     * Validate all booking conditions for a member
     * 
     * @param int $memberId The member ID
     * @param int $classId The class ID
     * @param string $bookingDate The booking date (YYYY-MM-DD)
     * @return array ['status' => bool, 'message' => string]
     */
    //     public function validateBooking($memberId, $classId, $bookingDate)
    // {
    //     // 1. Check if member has valid membership
    //     $membershipCheck = $this->hasValidMembership($memberId);
    //     if (!$membershipCheck['status']) {
    //         return $membershipCheck;
    //     }

    //     // Get class details to check branch
    //     $classScheduleTable = TableRegistry::get('class_schedule');
    //     $class = $classScheduleTable->get($classId);
    //     $branchId = $class->branch_id;

    //     // Si no hay membresía o es nula, saltarse las verificaciones que dependen de ella
    //     if (!isset($membershipCheck['membership']) || $membershipCheck['membership'] === null) {
    //         return [
    //             'status' => false,
    //             'message' => __('No tienes una membresía activa para reservar clases')
    //         ];
    //     }

    //     // 2. Check class limits for LIMITED memberships
    //     $limitCheck = $this->checkClassLimit(
    //         $memberId, 
    //         $membershipCheck['membership'], 
    //         $branchId, 
    //         $bookingDate
    //     );

    //     if (!$limitCheck['status']) {
    //         return $limitCheck;
    //     }

    //     // 3. If class is in another branch, check credits
    //     if ($membershipCheck['membership']->branch_id != $branchId) {
    //         $creditCheck = $this->checkBranchCredits(
    //             $memberId, 
    //             $membershipCheck['membership'], 
    //             $branchId
    //         );

    //         if (!$creditCheck['status']) {
    //             return $creditCheck;
    //         }
    //     }

    //     return [
    //         'status' => true,
    //         'message' => ''
    //     ];
    // }
    public function validateBooking($memberId, $classId, $bookingDate)
    {
        // 1. Check if member has valid membership
        $membershipCheck = $this->hasValidMembership($memberId);
        if (!$membershipCheck['status']) {
            return $membershipCheck;
        }

        // Get class details to check branch
        $classScheduleTable = TableRegistry::get('class_schedule');
        $class = $classScheduleTable->get($classId);
        $branchId = $class->branch_id;

        // 2. Check class limits for LIMITED memberships
        $limitCheck = $this->checkClassLimit(
            $memberId,
            $membershipCheck['membership'],
            $branchId,
            $bookingDate
        );

        if (!$limitCheck['status']) {
            return $limitCheck;
        }

        // 3. If class is in another branch, check credits
        if ($membershipCheck['membership']->branch_id != $branchId) {
            $creditCheck = $this->checkBranchCredits(
                $memberId,
                $membershipCheck['membership'],
                $branchId
            );

            if (!$creditCheck['status']) {
                return $creditCheck;
            }
        }

        return [
            'status' => true,
            'message' => ''
        ];
    }

    /**
     * Deduct a credit if booking is for another branch
     * 
     * @param int $memberId The member ID
     * @param int $branchId The branch ID of the class
     * @return bool Success status
     */
   
     public function deductCredit($memberId, $classId, $branchId)
     {
         $connection = \Cake\Datasource\ConnectionManager::get('default');
     
         // Obtener el costo de la clase
         $classScheduleTable = TableRegistry::get('class_schedule');
         $class = $classScheduleTable->find()
             ->select(['class_fees'])
             ->where(['id' => $classId])
             ->first();
     
         if (!$class) {
             $this->_registry->getController()->Flash->error(__('Clase no encontrada.'));
             return false;
         }
     
         $classFees = $class->class_fees;
     
         // Verificar si el miembro tiene suficientes créditos en la sucursal
         $gymMemberCreditsTable = TableRegistry::get('gym_member_credits');
         $memberCredits = $gymMemberCreditsTable->find()
             ->contain(['GymMemberMemberships'])
             ->where([
                 'GymMemberMemberships.member_id' => $memberId,
                 'gym_member_credits.branch_id' => $branchId,
                 'gym_member_credits.credits_remaining >=' => $classFees
             ])
             ->first();
     
         // Depuración: Verificar los datos obtenidos
        //  if (!$memberCredits) {
        //      $this->_registry->getController()->Flash->error(__('No tienes suficientes créditos para reservar esta clase.'));
        //      \Cake\Log\Log::write('debug', 'No se encontraron créditos suficientes. Datos de entrada: memberId=' . $memberId . ', branchId=' . $branchId . ', classFees=' . $classFees);
        //      return false;
        //  }
     
         // Registrar el uso de créditos en la tabla credit_usage
         $creditUsageTable = TableRegistry::get('credit_usage');
         $creditUsage = $creditUsageTable->newEntity([
             'gym_member_credit_id' => $memberCredits->id,
             'class_schedule_id' => $classId,
             'branch_id' => $branchId,
             'credits_used' => $classFees,
             'used_at' => date('Y-m-d H:i:s')
         ]);
     
         if (!$creditUsageTable->save($creditUsage)) {
             $this->_registry->getController()->Flash->error(__('Error al registrar el uso de créditos.'));
             return false;
         }
     
         // Actualizar los créditos restantes
         $memberCredits->credits_remaining -= $classFees;
         if (!$gymMemberCreditsTable->save($memberCredits)) {
             $this->_registry->getController()->Flash->error(__('Error al actualizar los créditos restantes.'));
             return false;
         }
     
        //  $this->_registry->getController()->Flash->success(__('Créditos descontados correctamente.'));
         return true;
     }


     // public function deductCredit($memberId, $classId, $branchId)
    // {
    //     // Obtener la clase y su costo (class_fees)
    //     $classScheduleTable = TableRegistry::get('class_schedule');
    //     $class = $classScheduleTable->find()
    //         ->select(['class_fees'])
    //         ->where(['id' => $classId])
    //         ->first();
    //     // Log::write('debug', 'Class IDddd: ' . $class);
    //     if (!$class) {
    //         return [
    //             'status' => false,
    //             'message' => __('Clase no encontrada'),
    //         ];
    //     }

    //     $classFees = $class->class_fees;

    //     // Verificar si la clase está en la misma sucursal que la membresía
    //     $membershipHistoryTable = TableRegistry::get('membership_history');
    //     $activeMembership = $membershipHistoryTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'membership_valid_to >=' => date('Y-m-d'),
    //             'membership_valid_from <=' => date('Y-m-d')
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //     if ($activeMembership && $activeMembership->branch_id == $branchId) {
    //         return [
    //             'status' => true,
    //             'message' => __('No se requieren créditos para la sucursal principal'),
    //         ];
    //     }

    //     // Buscar los créditos disponibles del miembro para la sucursal
    //     $connection = \Cake\Datasource\ConnectionManager::get('default');
    //     $query = "
    //         SELECT 
    //             gmc.id AS credit_id,
    //             gmc.credits_remaining AS remaining_credits
    //         FROM 
    //             gym_member_credits gmc
    //         INNER JOIN 
    //             gym_member_memberships gmm ON gmm.id = gmc.gym_member_membership_id
    //         WHERE 
    //             gmm.member_id = :member_id
    //             AND gmc.branch_id = :branch_id
    //             AND gmm.status = 'active'
    //         LIMIT 1
    //     ";

    //     $params = [
    //         'member_id' => $memberId,
    //         'branch_id' => $branchId
    //     ];

    //     $result = $connection->execute($query, $params)->fetch('assoc');

    //     if (!$result || $result['remaining_credits'] < $classFees) {
    //         return [
    //             'status' => false,
    //             'message' => __('No tienes suficientes créditos para inscribirte en esta clase'),
    //         ];
    //     }

    //     // Actualizar los créditos restantes
    //     $updateQuery = "
    //         UPDATE gym_member_credits 
    //         SET credits_remaining = credits_remaining - :class_fees, 
    //             updated_at = NOW() 
    //         WHERE id = :credit_id
    //     ";

    //     $updateParams = [
    //         'class_fees' => $classFees,
    //         'credit_id' => $result['credit_id']
    //     ];

    //     $connection->execute($updateQuery, $updateParams);

    //     // Registrar el uso de créditos en una tabla de historial
    //     $creditUsageTable = TableRegistry::get('credit_usage');
    //     $creditUsage = $creditUsageTable->newEntity([
    //         'gym_member_credit_id' => $result['credit_id'],
    //         'class_schedule_id' => $classId,
    //         'branch_id' => $branchId,
    //         'credits_used' => $classFees,
    //         'used_at' => date('Y-m-d H:i:s')
    //     ]);
    //     $creditUsageTable->save($creditUsage);

    //     return [
    //         'status' => true,
    //         'message' => __('Créditos descontados correctamente'),
    //     ];
    // }





    //     public function deductCredit($memberId, $branchId)
    // {
    //     $membershipHistoryTable = TableRegistry::get('membership_history');
    //     $activeMembership = $membershipHistoryTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'membership_valid_to >=' => date('Y-m-d'),
    //             'membership_valid_from <=' => date('Y-m-d')
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //     // Si la clase está en la misma sucursal que la membresía, no se necesitan créditos
    //     if ($activeMembership && $activeMembership->branch_id == $branchId) {
    //         return true;
    //     }

    //     // Usar la misma lógica de consulta SQL directa
    //     $connection = \Cake\Datasource\ConnectionManager::get('default');

    //     // Primero, obtener el registro de créditos
    //     $query = "SELECT mc.id, mc.credits as remaining_credits
    //               FROM membership_credits mc
    //               JOIN membership m ON mc.membership_id = m.id
    //               JOIN membership_history mh ON mh.selected_membership = m.id
    //               WHERE mh.member_id = :member_id
    //               AND m.branch_id = :branch_id
    //               AND mh.membership_valid_to >= :today
    //               AND mc.credits > 0
    //               ORDER BY mh.id DESC
    //               LIMIT 1";

    //     $params = [
    //         'member_id' => $memberId,
    //         'branch_id' => $branchId,
    //         'today' => date('Y-m-d')
    //     ];

    //     $result = $connection->execute($query, $params)->fetch('assoc');

    //     if ($result) {
    //         // Actualizar créditos
    //         $updateQuery = "UPDATE membership_credits 
    //                         SET credits = credits - 1, 
    //                             updated_at = NOW() 
    //                         WHERE id = :id";

    //         $updateParams = ['id' => $result['id']];
    //         $connection->execute($updateQuery, $updateParams);

    //         return true;
    //     }

    //     return false;
    // }
    // public function deductCredit($memberId, $branchId)
    // {
    //     $membershipHistoryTable = TableRegistry::get('membership_history');
    //     $activeMembership = $membershipHistoryTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'membership_valid_to >=' => date('Y-m-d'),
    //             'membership_valid_from <=' => date('Y-m-d')
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //     // If class is in the same branch as membership, no credits needed
    //     if ($activeMembership && $activeMembership->branch_id == $branchId) {
    //         return true;
    //     }

    //     // Deduct a credit
    //     $membershipCreditsTable = TableRegistry::get('membership_credits');
    //     $credits = $membershipCreditsTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'branch_id' => $branchId,
    //             'expiry_date >=' => date('Y-m-d'),
    //             'remaining_credits >' => 0
    //         ])
    //         ->order(['id' => 'DESC'])
    //         ->first();

    //     if ($credits) {
    //         $credits->remaining_credits -= 1;
    //         $credits->last_used_date = date('Y-m-d');
    //         return $membershipCreditsTable->save($credits) ? true : false;
    //     }

    //     return false;
    // }

    /**
     * Get membership class limit status for a member
     * 
     * @param int $memberId The member ID to check
     * @param string $bookingDate The booking date (YYYY-MM-DD) to check against
     * @return array ['limit_type' => string, 'days_used' => int, 'days_total' => int, 'period' => string]
     */
    public function getMembershipLimitStatus($memberId, $bookingDate = null)
    {
        $bookingDate = $bookingDate ?: date('Y-m-d');
        $today = date('Y-m-d');

        // Get active membership
        $membershipCheck = $this->hasValidMembership($memberId);

        // Verificar si hay membresía válida
        if (!$membershipCheck['status'] || !$membershipCheck['membership']) {
            return [
                'limit_type' => 'NONE',
                'days_used' => 0,
                'days_total' => 0,
                'period' => '',
                'message' => $membershipCheck['message'] ?? __('No tienes una membresía activa')
            ];
        }

        $membership = $membershipCheck['membership'];

        // Obtener los detalles de la membresía
        $membershipTable = TableRegistry::get('membership');
        if (empty($membership->selected_membership)) {
            Log::write('error', 'El campo selected_membership está vacío o no definido.');
            return [
                'limit_type' => 'ERROR',
                'days_used' => 0,
                'days_total' => 0,
                'period' => '',
                'message' => __('Información de membresía no disponible')
            ];
        }
        
        try {
            $membershipDetails = $membershipTable->get($membership->selected_membership);
        } catch (\Exception $e) {
            Log::write('error', 'No se pudo obtener detalles de membresía: ' . $e->getMessage());
            return [
                'limit_type' => 'ERROR',
                'days_used' => 0,
                'days_total' => 0,
                'period' => '',
                'message' => __('Error al obtener detalles de membresía')
            ];
        }

        // Verificar si la membresía tiene límite o es ilimitada
        if (
            !empty($membershipDetails->membership_class_limit) &&
            strtoupper($membershipDetails->membership_class_limit) === 'UNLIMITED'
        ) {
            return [
                'limit_type' => 'UNLIMITED',
                'days_used' => 0,
                'days_total' => 0,
                'period' => '',
                'message' => __('Membresía ilimitada')
            ];
        }

        // Si no tiene limit_days establecido, consideramos que es ilimitada
        if (empty($membershipDetails->limit_days)) {
            return [
                'limit_type' => 'UNLIMITED',
                'days_used' => 0,
                'days_total' => 0,
                'period' => '',
                'message' => __('Sin límite de clases')
            ];
        }

        // Si llegamos aquí, la membresía tiene límite
        $limitDays = (int)$membershipDetails->limit_days;
        $limitation = $membershipDetails->limitation ?: 'per_month';

        // Calcular el rango de fechas según el tipo de limitación
        $startDate = '';
        $endDate = '';
        $periodText = '';

        if ($limitation === 'per_week') {
            // Obtener las fechas de inicio (lunes) y fin (domingo) de la semana
            $dayOfWeek = date('N', strtotime($bookingDate));
            $startDate = date('Y-m-d', strtotime($bookingDate . " -" . ($dayOfWeek - 1) . " days"));
            $endDate = date('Y-m-d', strtotime($startDate . " +6 days"));
            $periodText = __('esta semana');
        } else { // per_month
            // Obtener las fechas de inicio y fin del mes
            $startDate = date('Y-m-01', strtotime($bookingDate));
            $endDate = date('Y-m-t', strtotime($bookingDate));
            $periodText = __('este mes');
        }

        // Obtener las reservas del período actual para este miembro en esta sucursal
        $classBookingTable = TableRegistry::get('class_booking');

        // Contar solo las reservas pasadas como "usadas"
        $usedBookings = $classBookingTable->find()
            ->where([
                'member_id' => $memberId,
                'booking_date >=' => $startDate,
                'booking_date <=' => $endDate,
                'booking_date <' => $today, // Solo contar reservas hasta hoy
                'status !=' => 'Cancelled'
            ])
            ->matching('ClassSchedule', function ($q) use ($membership) {
                return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
            })
            ->count();

        // Contar todas las reservas (pasadas y futuras) para el total
        $totalBookings = $classBookingTable->find()
            ->where([
                'member_id' => $memberId,
                'booking_date >=' => $startDate,
                'booking_date <=' => $endDate,
                'status !=' => 'Cancelled'
            ])
            ->matching('ClassSchedule', function ($q) use ($membership) {
                return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
            })
            ->count();

        $remainingBookings = $limitDays - $totalBookings;

        return [
            'limit_type' => 'LIMITED',
            'days_used' => $usedBookings,
            'days_total' => $limitDays,
            'days_booked' => $totalBookings,
            'days_remaining' => $remainingBookings,
            'period' => $limitation,
            'period_text' => $periodText,
            'message' => __('Clases usadas: {0}/{1} {2} (Reservadas: {3})', $usedBookings, $limitDays, $periodText, $totalBookings)
        ];
    }
    // public function getMembershipLimitStatus($memberId, $bookingDate = null)
    // {
    //     $bookingDate = $bookingDate ?: date('Y-m-d');
    //     $today = date('Y-m-d');

    //     // Get active membership
    //     $membershipCheck = $this->hasValidMembership($memberId);
    //     if (!$membershipCheck['status']) {
    //         return [
    //             'limit_type' => 'NONE',
    //             'days_used' => 0,
    //             'days_total' => 0,
    //             'period' => '',
    //             'message' => $membershipCheck['message']
    //         ];
    //     }

    //     $membership = $membershipCheck['membership'];

    //     // If membership is UNLIMITED, return unlimited status
    //     if ($membership->membership_class_limit === 'Unlimited') {
    //         return [
    //             'limit_type' => 'UNLIMITED',
    //             'days_used' => 0,
    //             'days_total' => 0,
    //             'period' => '',
    //             'message' => __('Membresía ilimitada')
    //         ];
    //     }

    //     // Check if membership_id is NULL
    //     if (empty($membership->id)) {
    //         return [
    //             'limit_type' => 'LIMITED',
    //             'days_used' => 0,
    //             'days_total' => 0,
    //             'period' => '',
    //             'message' => __('Información de membresía no disponible')
    //         ];
    //     }

    //     // Get membership details
    //     $membershipTable = TableRegistry::get('membership');
    //     $membershipDetails = $membershipTable->get($membership->selected_membership);

    //     // If no limit_days is set, return unlimited
    //     if (empty($membershipDetails->limit_days)) {
    //         return [
    //             'limit_type' => 'UNLIMITED',
    //             'days_used' => 0,
    //             'days_total' => 0,
    //             'period' => '',
    //             'message' => __('Sin límite de clases')
    //         ];
    //     }

    //     $limitDays = (int)$membershipDetails->limit_days;
    //     $limitation = $membershipDetails->limitation ?: 'per_month';

    //     // Calculate date range based on limitation type
    //     $startDate = '';
    //     $endDate = '';
    //     $periodText = '';

    //     if ($limitation === 'per_week') {
    //         // Get the week start (Monday) and end (Sunday) dates
    //         $dayOfWeek = date('N', strtotime($bookingDate));
    //         $startDate = date('Y-m-d', strtotime($bookingDate . " -" . ($dayOfWeek - 1) . " days"));
    //         $endDate = date('Y-m-d', strtotime($startDate . " +6 days"));
    //         $periodText = __('esta semana');
    //     } else { // per_month
    //         // Get the month start and end dates
    //         $startDate = date('Y-m-01', strtotime($bookingDate));
    //         $endDate = date('Y-m-t', strtotime($bookingDate));
    //         $periodText = __('este mes');
    //     }

    //     // Get current period's bookings for this member in this branch
    //     $classBookingTable = TableRegistry::get('class_booking');

    //     // Count only past bookings as "used"
    //     $usedBookings = $classBookingTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'booking_date >=' => $startDate,
    //             'booking_date <=' => $endDate,
    //             'booking_date <' => $today, // Only count bookings up to today
    //             'status !=' => 'Cancelled'
    //         ])
    //         ->matching('ClassSchedule', function ($q) use ($membership) {
    //             return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
    //         })
    //         ->count();

    //     // Count all bookings (past and future) for the total
    //     $totalBookings = $classBookingTable->find()
    //         ->where([
    //             'member_id' => $memberId,
    //             'booking_date >=' => $startDate,
    //             'booking_date <=' => $endDate,
    //             'status !=' => 'Cancelled'
    //         ])
    //         ->matching('ClassSchedule', function ($q) use ($membership) {
    //             return $q->where(['ClassSchedule.branch_id' => $membership->branch_id]);
    //         })
    //         ->count();

    //     $remainingBookings = $limitDays - $totalBookings;

    //     return [
    //         'limit_type' => 'LIMITED',
    //         'days_used' => $usedBookings,
    //         'days_total' => $limitDays,
    //         'days_booked' => $totalBookings,
    //         'days_remaining' => $remainingBookings,
    //         'period' => $limitation,
    //         'period_text' => $periodText,
    //         'message' => __('Clases usadas: {0}/{1} {2} (Reservadas: {3})', $usedBookings, $limitDays, $periodText, $totalBookings)
    //     ];
    // }

    /**
     * Get branch credits information for a member
     * 
     * @param int $memberId The member ID to check
     * @return array Array of branch credits information
     */

     public function getBranchCredits($memberId)
     {
         // Obtener la membresía activa
         $membershipCheck = $this->hasValidMembership($memberId);
         if (!$membershipCheck['status'] || !$membershipCheck['membership']) {
             return [];
         }
     
         $membership = $membershipCheck['membership'];
         $membershipBranchId = $membership->branch_id;
     
         // Obtener todas las sucursales
         $branchTable = TableRegistry::get('gym_branch');
         $branches = $branchTable->find('all')->toArray();
     
         // Buscar IDs de membresías activas del usuario
         $gymMemberMembershipsTable = TableRegistry::get('gym_member_memberships');
         $activeMembershipIds = $gymMemberMembershipsTable->find()
             ->select(['id'])
             ->where(['member_id' => $memberId, 'status' => 'active'])
             ->extract('id')
             ->toArray();
     
         if (empty($activeMembershipIds)) {
             return [];
         }
     
         // Buscar créditos por sucursal (excepto la principal)
         $membershipCreditsTable = TableRegistry::get('gym_member_credits');
         $branchCredits = [];
         foreach ($branches as $branch) {
             if ($branch->id == $membershipBranchId) {
                 continue;
             }
             $credit = $membershipCreditsTable->find()
                 ->where([
                     'gym_member_membership_id IN' => $activeMembershipIds,
                     'branch_id' => $branch->id
                 ])
                 ->first();
     
             $branchCredits[] = [
                 'branch_id' => $branch->id,
                 'branch_name' => $branch->name,
                 'credits' => $credit ? $credit->credits_remaining : 0,
                 'expiry_date' => $credit ? $credit->updated_at : null
             ];
         }
         return $branchCredits;
     }
    // public function getBranchCredits($memberId)
    // {
    //     $membershipCheck = $this->hasValidMembership($memberId);
    // if (!$membershipCheck['status'] || !$membershipCheck['membership']) {
    //     return [];
    // }

    // $membership = $membershipCheck['membership'];

    // // Check if branch_id is NULL
    // if (!isset($membership->branch_id) || empty($membership->branch_id)) {
    //     return [];
    // }

    //     // // Get active membership
    //     // $membershipCheck = $this->hasValidMembership($memberId);
    //     // if (!$membershipCheck['status']) {
    //     //     return [];
    //     // }

    //     // $membership = $membershipCheck['membership'];

    //     // // Check if branch_id is NULL
    //     // if (empty($membership->branch_id)) {
    //     //     return [];
    //     // }

    //     $membershipBranchId = $membership->branch_id;

    //     // Get all branches
    //     $branchTable = TableRegistry::get('gym_branch');
    //     $branches = $branchTable->find('all')->toArray();

    //     // Get credits for each branch
    //     $membershipCreditsTable = TableRegistry::get('membership_credits');
    //     $branchCredits = [];

    //     foreach ($branches as $branch) {
    //         // Skip member's home branch
    //         if ($branch->id == $membershipBranchId) {
    //             continue;
    //         }

    //         $credits = $membershipCreditsTable->find()
    //             ->where([
    //                 'member_id' => $memberId,
    //                 'branch_id' => $branch->id,
    //             ])
    //             ->order(['id' => 'DESC'])
    //             ->first();

    //         $branchCredits[] = [
    //             'branch_id' => $branch->id,
    //             'branch_name' => $branch->branch_name,
    //             'credits' => $credits ? $credits->remaining_credits : 0,
    //             'expiry_date' => $credits ? $credits->expiry_date : null
    //         ];
    //     }

    //     return $branchCredits;
    // }
}
