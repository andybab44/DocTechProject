<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    public function __construct(private readonly InventoryItem $item) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low stock alert: ' . $this->item->name)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('The inventory item "' . $this->item->name . '" is running low.')
            ->line('Current stock: ' . $this->item->quantity . ' ' . $this->item->unit)
            ->line('Low-stock threshold: ' . $this->item->low_stock_threshold . ' ' . $this->item->unit)
            ->line('Please restock this item soon.');
    }
}
