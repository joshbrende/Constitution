<?php

namespace App\Enums;

enum CertificateApplicationStatus: string
{
    case ExamPassed = 'exam_passed';
    case ReceiptIssued = 'receipt_issued';
    case PaymentPending = 'payment_pending';
    case PaymentConfirmed = 'payment_confirmed';
    case PresidiumPending = 'presidium_pending';
    case PresidiumApproved = 'presidium_approved';
    case PrintReady = 'print_ready';
    case Printed = 'printed';
    case ReadyForCollection = 'ready_for_collection';
    case Collected = 'collected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ExamPassed => 'Exam passed',
            self::ReceiptIssued => 'Receipt issued',
            self::PaymentPending => 'Awaiting payment',
            self::PaymentConfirmed => 'Payment confirmed',
            self::PresidiumPending => 'Awaiting Presidium approval',
            self::PresidiumApproved => 'Presidium approved',
            self::PrintReady => 'Ready to print',
            self::Printed => 'Printed',
            self::ReadyForCollection => 'Ready for collection',
            self::Collected => 'Collected',
            self::Cancelled => 'Cancelled',
        };
    }
}
