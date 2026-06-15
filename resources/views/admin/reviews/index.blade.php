@extends('layouts.app')

@section('title', __('app.reviews.admin_title'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-stone-800">{{ __('app.reviews.admin_title') }}</h1>
        <p class="text-stone-500 mt-1">{{ __('app.reviews.admin_subtitle') }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_reviewer') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_reviewee') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_job') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_rating') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_comment') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_status') }}</th>
                <th class="px-6 py-3 text-left font-medium text-stone-500 uppercase tracking-wider">{{ __('app.reviews.col_date') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($reviews as $review)
            <tr class="hover:bg-stone-50 {{ $review->is_visible ? '' : 'opacity-60' }}">
                <td class="px-6 py-4 text-stone-800">{{ $review->reviewer->name }}</td>
                <td class="px-6 py-4 text-stone-800">{{ $review->reviewee->name }}</td>
                <td class="px-6 py-4 text-stone-600">
                    @if($review->workJob)
                    <a href="{{ route('work-jobs.show', $review->workJob) }}" class="text-teal-700 hover:underline">
                        {{ $review->workJob->title }}
                    </a>
                    @else
                    —
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-0.5 text-yellow-500 font-semibold">
                        {{ $review->rating }}/5
                    </span>
                </td>
                <td class="px-6 py-4 text-stone-600 max-w-xs truncate">{{ $review->comment ?? '—' }}</td>
                <td class="px-6 py-4">
                    @if($review->is_visible)
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-green-100 text-green-700">{{ __('app.reviews.status_visible') }}</span>
                    @else
                        <span class="inline-block text-xs font-medium rounded-full px-2.5 py-0.5 bg-stone-100 text-stone-500">{{ __('app.reviews.status_hidden') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-stone-500">{{ $review->created_at->toFormattedDateString() }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm text-teal-700 hover:underline">
                                {{ $review->is_visible ? __('app.reviews.btn_hide') : __('app.reviews.btn_show') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                              onsubmit="return confirm('{{ __('app.reviews.delete_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">{{ __('app.reviews.btn_delete') }}</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-stone-400">{{ __('app.reviews.no_reviews') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($reviews->hasPages())
<div class="mt-4">{{ $reviews->links() }}</div>
@endif
@endsection
