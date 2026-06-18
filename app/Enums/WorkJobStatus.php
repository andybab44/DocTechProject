<?php

namespace App\Enums;

enum WorkJobStatus: string
{
    case AwaitingAcceptance = 'awaiting_acceptance';
    case InProgress         = 'in_progress';
    case InReview           = 'in_review';
    case NeedsRevision      = 'needs_revision';
    case ReadyForDelivery   = 'ready_for_delivery';
    case Delivered          = 'delivered';
    case Cancelled          = 'cancelled';

    public function label(): string
    {
        return __('app.work_job_statuses.' . $this->value);
    }

    public function color(): string
    {
        return match($this) {
            self::AwaitingAcceptance => 'yellow',
            self::InProgress         => 'blue',
            self::InReview           => 'purple',
            self::NeedsRevision      => 'orange',
            self::ReadyForDelivery   => 'teal',
            self::Delivered          => 'green',
            self::Cancelled          => 'red',
        };
    }
    public function barColour(): string
    {
        return match($this) {
            self::AwaitingAcceptance => 'bg-yellow-400',
            self::InProgress         => 'bg-blue-500',
            self::InReview           => 'bg-purple-500',
            self::NeedsRevision      => 'bg-orange-400',
            self::ReadyForDelivery   => 'bg-teal-400',
            self::Delivered          => 'bg-green-500',
            self::Cancelled          => 'bg-red-400',
        ];
    }}
