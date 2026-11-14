{{-- نشانک (badge) وضعیت پست.  <x-post-status :post="$post" /> --}}
@props(['post'])

@php($status = $post->effective_status)

<span {{ $attributes->class(['inline-block rounded-full border px-2.5 py-0.5 text-xs', $status->badgeClasses()]) }}>{{ $status->label() }}</span>
