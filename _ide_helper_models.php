<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $work_date
 * @property \Illuminate\Support\Carbon|null $clock_in
 * @property \Illuminate\Support\Carbon|null $clock_out
 * @property \Illuminate\Support\Carbon|null $break_start
 * @property \Illuminate\Support\Carbon|null $break_end
 * @property numeric|null $total_hours
 * @property numeric|null $regular_hours
 * @property numeric|null $overtime_hours
 * @property string $status
 * @property int $free_meal_taken
 * @property string|null $notes
 * @property int|null $recorded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $recordedBy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord forWeek($startDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereBreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereBreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereClockIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereClockOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereFreeMealTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereOvertimeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereRecordedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereRegularHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceRecord whereWorkDate($value)
 */
	class AttendanceRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $guest_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereGuestToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $vendor_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $line_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Stall $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereLineTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereVendorId($value)
 */
	class CartItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property string|null $description
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property numeric $hourly_rate
 * @property numeric|null $daily_rate
 * @property string $pay_type
 * @property numeric|null $fixed_weekly_amount
 * @property \Illuminate\Support\Carbon $effective_from
 * @property \Illuminate\Support\Carbon|null $effective_until
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereDailyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereEffectiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereEffectiveUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereFixedWeeklyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage wherePayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeWage whereUserId($value)
 */
	class EmployeeWage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $category_id
 * @property int $recorded_by
 * @property string $description
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $expense_date
 * @property string|null $receipt_number
 * @property string|null $vendor
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExpenseCategory|null $category
 * @property-read \App\Models\User|null $recordedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense byCategory($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereRecordedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereVendor($value)
 */
	class Expense extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereUpdatedAt($value)
 */
	class ExpenseCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $order_group_id
 * @property int|null $vendor_id
 * @property string $order_number
 * @property string $order_reference
 * @property int|null $user_id
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_email
 * @property string|null $guest_token
 * @property string $status
 * @property string $delivery_type
 * @property string $delivery_time
 * @property string|null $scheduled_datetime
 * @property string|null $delivery_address
 * @property string|null $estimated_ready_time
 * @property numeric $total_amount
 * @property string|null $payment_method
 * @property string $payment_status
 * @property string $order_type
 * @property string|null $service_type
 * @property string|null $special_instructions
 * @property string|null $notes
 * @property int $has_complaint
 * @property string|null $complaint_details
 * @property string|null $resolution_notes
 * @property string|null $dispute_resolved_at
 * @property string $refund_status
 * @property string|null $refund_reason
 * @property string|null $refunded_at
 * @property string|null $admin_notes
 * @property string|null $admin_override_at
 * @property int|null $admin_override_by
 * @property string|null $flagged_at
 * @property string|null $flag_reason
 * @property int|null $estimated_completion
 * @property string $user_type
 * @property array<array-key, mixed>|null $guest_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $amount_subtotal
 * @property int $amount_total
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\OrderGroup|null $orderGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\Stall|null $stall
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\User|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAdminOverrideAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAdminOverrideBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAmountSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAmountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereComplaintDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDisputeResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEstimatedCompletion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEstimatedReadyTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereFlagReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereFlaggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereGuestDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereGuestToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereHasComplaint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRefundReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereScheduledDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSpecialInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereVendorId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $payer_type
 * @property int|null $user_id
 * @property string|null $guest_token
 * @property string $payment_method
 * @property string $payment_status
 * @property int $amount_total
 * @property string $currency
 * @property array<array-key, mixed> $billing_contact
 * @property string|null $payment_provider
 * @property string|null $provider_intent_id
 * @property string|null $provider_client_secret
 * @property array<array-key, mixed>|null $cart_snapshot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereAmountTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereBillingContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereCartSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereGuestToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup wherePayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup wherePaymentProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereProviderClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereProviderIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderGroup whereUserId($value)
 */
	class OrderGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_name
 * @property int $quantity
 * @property numeric $unit_price
 * @property int $line_total
 * @property float $subtotal
 * @property string|null $special_instructions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $price
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereLineTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSpecialInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_group_id
 * @property int|null $user_id
 * @property int|null $stall_id
 * @property string $payment_method
 * @property numeric $amount
 * @property string $status
 * @property string $provider
 * @property string|null $provider_payment_id
 * @property string|null $provider_source_id
 * @property string|null $receipt_url
 * @property array<array-key, mixed>|null $provider_response
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_amount
 * @property-read string $payment_method_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \App\Models\OrderGroup $orderGroup
 * @property-read \App\Models\Stall|null $stall
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment byStall($stallId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment cash()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment online()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment successful()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereOrderGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProviderPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProviderResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProviderSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReceiptUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserId($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $period_start
 * @property \Illuminate\Support\Carbon $period_end
 * @property int $days_worked
 * @property int $days_present
 * @property int $days_late
 * @property int $days_half_day
 * @property int $days_absent
 * @property numeric $total_hours
 * @property numeric $daily_rate
 * @property numeric $gross_pay
 * @property numeric $net_pay
 * @property string $status
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $paid_date
 * @property string|null $notes
 * @property int|null $generated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $generatedBy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDailyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDaysAbsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDaysHalfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDaysLate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDaysPresent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereDaysWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereGeneratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereGrossPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereNetPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll wherePeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payroll whereUserId($value)
 */
	class Payroll extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string|null $image
 * @property bool $is_available
 * @property bool $is_published
 * @property int|null $preparation_time
 * @property int|null $created_by
 * @property int $stock_quantity
 * @property int $low_stock_alert
 * @property int $stall_id
 * @property string $description
 * @property int|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $image_url
 * @property-read string $stock_status
 * @property-read string $stock_status_color
 * @property-read string $stock_status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Stall $stall
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product inStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product lowStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product outOfStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereLowStockAlert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePreparationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $stall_id
 * @property int $tenant_id
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $period_start
 * @property \Illuminate\Support\Carbon $period_end
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon|null $paid_date
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_period
 * @property-read bool $is_overdue
 * @property-read \App\Models\Stall $stall
 * @property-read \App\Models\User $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment wherePeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment wherePeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RentalPayment whereUpdatedAt($value)
 */
	class RentalPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $order_id
 * @property int $rating
 * @property string|null $comment
 * @property bool $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $owner_id
 * @property int|null $tenant_id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $description
 * @property string|null $logo
 * @property string|null $image
 * @property string|null $cover_photo
 * @property string|null $logo_url
 * @property string|null $contact_number
 * @property \Illuminate\Support\Carbon|null $opening_time
 * @property \Illuminate\Support\Carbon|null $closing_time
 * @property string $location
 * @property numeric $rental_fee
 * @property bool $is_active
 * @property numeric $commission_rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RentalPayment> $overduePayments
 * @property-read int|null $overdue_payments_count
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RentalPayment> $rentalPayments
 * @property-read int|null $rental_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StallReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereClosingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereCoverPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereOpeningTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereRentalFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall withoutTrashed()
 */
	class Stall extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $stall_id
 * @property \Illuminate\Support\Carbon $report_date
 * @property int $gross_sales
 * @property float $commission_rate
 * @property int $commission_amount
 * @property int $net_sales
 * @property bool $is_paid
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payment_reference
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Stall $stall
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereGrossSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereNetSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StallReport whereUpdatedAt($value)
 */
	class StallReport extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 * 
 * Common Spatie Permission helper methods:
 *
 * @method bool hasRole(string|array $roles, string|null $guard = null)
 * @method bool hasAnyRole(string|array $roles, string|null $guard = null)
 * @method bool hasAllRoles(string|array $roles, string|null $guard = null)
 * @method \Spatie\Permission\Models\Role|array assignRole(...$roles)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @method \Illuminate\Database\Eloquent\Relations\HasMany orders()
 * @method \Spatie\Permission\Models\Role[] syncRoles(...$roles)
 * @method void removeRole(string|\Spatie\Permission\Contracts\Role $role)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Contracts\Permission $permission, string|null $guard = null)
 * @method bool hasAnyPermission(string|array ...$permissions)
 * @method \Spatie\Permission\Models\Permission|array givePermissionTo(...$permissions)
 * @method \Spatie\Permission\Models\Permission[] syncPermissions(...$permissions)
 * @method void revokePermissionTo(string|\Spatie\Permission\Contracts\Permission $permission)
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $preferred_notification_channel
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $terms_accepted_at
 * @property \Illuminate\Support\Carbon|null $verification_sent_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $phone
 * @property string|null $type
 * @property bool $is_active
 * @property bool $is_staff
 * @property string $daily_rate
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property string|null $profile_picture
 * @property int $is_guest
 * @property int $can_pay_onsite
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $admin_stall_id
 * @property-read \App\Models\Stall|null $adminStall
 * @property-read \App\Models\Stall|null $assignedStall
 * @property-read \App\Models\Cart|null $cart
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmployeeWage> $employeeWages
 * @property-read int|null $employee_wages_count
 * @property-read mixed $profile_picture_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderGroup> $orderGroups
 * @property-read int|null $order_groups_count
 * @property-read int|null $orders_count
 * @property-read \App\Models\Stall|null $ownedStall
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payroll> $payrolls
 * @property-read int|null $payrolls_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RentalPayment> $rentalPayments
 * @property-read int|null $rental_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Stall|null $stall
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $vendorOrders
 * @property-read int|null $vendor_orders_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdminStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCanPayOnsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDailyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreferredNotificationChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTermsAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Filament\Models\Contracts\FilamentUser {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $week_start
 * @property \Illuminate\Support\Carbon $week_end
 * @property numeric $total_hours
 * @property numeric $regular_hours
 * @property numeric $overtime_hours
 * @property numeric $hourly_rate
 * @property numeric $regular_pay
 * @property numeric $overtime_pay
 * @property numeric $bonuses
 * @property numeric $total_payout
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $paid_date
 * @property string|null $notes
 * @property int|null $processed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $processedBy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereBonuses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereOvertimeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereOvertimePay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereRegularHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereRegularPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereTotalPayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereWeekEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyPayout whereWeekStart($value)
 */
	class WeeklyPayout extends \Eloquent {}
}

