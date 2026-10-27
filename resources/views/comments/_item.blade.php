{{--
    یک کامنت (اصلی یا ریپلای). هر کدام بلوک مستقل خودشان را دارند:
    متن، فرم ویرایش، دکمه‌ها و (فقط برای کامنت اصلی) فرم ریپلای و لیست ریپلای‌ها.

    ورودی‌ها:
      $comment  → مدل کامنت
      $isReply  → اگر true باشد یعنی ریپلای است (دکمه Reply و ریپلای تو در تو ندارد)
--}}
@php
    $isReply   = $isReply ?? false;
    $isPending = ! $comment->isApproved();
    $viewerIsAdmin = auth()->user()?->role === 'admin';
    $viewerIsOwner = auth()->id() === $comment->user_id;
@endphp

<div id="comment-{{ $comment->id }}"
     class="{{ $isReply ? 'ml-12 mt-5 pl-5 border-l-2 border-gray-200' : 'border-b border-gray-200 py-5' }}">

    {{-- هدر: آواتار، نام، تاریخ --}}
    <div class="flex items-center gap-3">

        <div class="{{ $isReply ? 'w-9 h-9' : 'w-12 h-12' }} rounded-full overflow-hidden
                    flex items-center justify-center font-bold bg-indigo-100 text-fuchsia-900">
            @if($comment->user->avatar)
                <img src="{{ Storage::url($comment->user->avatar) }}" alt="" class="w-full h-full object-cover">
            @else
                {{ mb_substr($comment->user->name, 0, 1) }}
            @endif
        </div>

        <div>
            <p class="font-semibold text-gray-900">
                {{ $comment->user->name }}
            </p>

            <p class="text-sm text-gray-400">
                {{ $comment->created_at->format('Y-m-d H:i') }}
            </p>
        </div>

    </div>

    {{-- متن کامنت --}}
    <div id="comment-view-{{ $comment->id }}">
        <p class="mt-3 text-gray-700">
            {{ $comment->body }}
        </p>

        {{-- کامنت تاییدنشده: فقط برای نویسنده (پیش‌نمایش) و ادمین دیده می‌شود --}}
        @if($isPending)
            <div class="mt-3 rounded-xl border !border-amber-400/40 bg-amber-400/10 px-4 py-3 text-sm text-amber-200">
                @if($viewerIsOwner)
                    تا زمانی که این کامنت مورد تایید ادمین قرار نگیرد فقط برای خودتان قابل مشاهده خواهد بود.
                @else
                    این کامنت هنوز تایید نشده و فقط برای ادمین و نویسنده‌اش قابل مشاهده است.
                @endif

                @if($viewerIsAdmin)
                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline-block mr-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-create btn-sm">تایید</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- فرم ویرایش --}}
    @can('update_comment', $comment)
        <div id="comment-edit-{{ $comment->id }}" class="hidden mt-3">

            <form action="{{ route('comments.update', $comment) }}" method="POST">
                @csrf
                @method('PUT')

                <textarea
                    name="body"
                    rows="{{ $isReply ? 3 : 4 }}"
                    class="w-full px-4 py-3 border border-gray-300
                           rounded-lg resize-none
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-500"
                >{{ $comment->body }}</textarea>

                <div class="flex items-center gap-2 mt-3">
                    <button type="submit" class="btn-update btn-sm">
                        تایید
                    </button>

                    <button type="button" onclick="cancelEdit({{ $comment->id }})" class="btn-cancel btn-sm">
                        لغو
                    </button>
                </div>
            </form>

        </div>
    @endcan

    {{-- دکمه‌ها: Reply / ویرایش / حذف (برای کامنت اصلی و ریپلای یکسان) --}}
    <div id="comment-actions-{{ $comment->id }}">
        <div class="flex flex-wrap items-center gap-2 mt-3">

            @auth
                @if(! $isReply && ! $isPending)
                    <button type="button" onclick="replyToComment({{ $comment->id }})" class="btn-glass btn-sm">
                        Reply
                    </button>
                @endif
            @endauth

            @can('update_comment', $comment)
                <button type="button" onclick="editComment({{ $comment->id }})" class="btn-edit btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                    </svg>
                    ویرایش
                </button>
            @endcan

            @can('delete_comment', $comment)
                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline"
                      onsubmit="return confirm('مطمئنی می‌خوای این کامنت رو حذف کنی؟')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-delete btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                        حذف
                    </button>
                </form>
            @endcan

        </div>
    </div>

    {{-- کامنت تاییدنشده: ریپلای ندارد و ریپلای‌هایش هم نمایش داده نمی‌شوند --}}
    @if(! $isReply && ! $isPending)

        {{-- فرم ریپلای (فقط برای کامنت اصلیِ تاییدشده) --}}
        @auth
            <form id="reply-form-{{ $comment->id }}"
                  action="{{ route('comments.reply', $comment) }}"
                  method="POST" class="hidden mt-4">
                @csrf

                <textarea name="reply_body" rows="3"
                          placeholder="Write a reply to {{ $comment->user->name }}"
                          class="w-full px-4 py-3 border border-gray-300
                                 rounded-lg resize-none
                                 focus:outline-none focus:ring-2
                                 focus:ring-indigo-500"></textarea>

                @error('reply_body')
                <p class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </p>
                @enderror

                <button type="submit" class="btn-create btn-sm mt-2">
                    Reply
                </button>
            </form>
        @endauth

        {{-- ریپلای‌ها: هر کدام همین قالب را با دکمه‌های خودشان می‌گیرند --}}
        @foreach($comment->replies as $reply)
            @include('comments._item', ['comment' => $reply, 'isReply' => true])
        @endforeach

    @endif

</div>
