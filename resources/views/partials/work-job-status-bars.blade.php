{{--
    Reusable work-job status breakdown bars.

    Required:  $workJobStats  — array{total: int, by_status: Collection}
    Optional:  $gridClass     — Tailwind grid class for the wrapper div
                               default: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
--}}
@php $total = max($workJobStats['total'], 1); @endphp
<div class="grid {{ $gridClass ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' }} gap-x-8 gap-y-3">
    @foreach (\App\Enums\WorkJobStatus::cases() as $status)
    @php $count = $workJobStats['by_status']->get($status->value)?->total ?? 0; @endphp
    @if ($count > 0 || ($showEmpty ?? true))
    <div>
        <div class="flex justify-between text-xs text-stone-600 mb-0.5">
            <span>{{ $status->label() }}</span>
            <span class="font-medium">{{ $count }}</span>
        </div>
        <div class="w-full bg-stone-100 rounded-full h-2">
            <div class="{{ $status->barColour() }} h-2 rounded-full transition-all"
                 style="width: {{ round(($count / $total) * 100) }}%"></div>
        </div>
    </div>
    @endif
    @endforeach
</div>
