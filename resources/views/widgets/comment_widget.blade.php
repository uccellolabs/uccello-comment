<div class="card">
    <div class="card-content">
        <span class="card-title">
            <i class="material-icons left primary-text">speaker_notes</i>
            {!! $config['data']->title ?? uctrans('comment', $mComment) !!}
        </span>

        <div class="uc-comments">
            <div class="uc-header row">
                <div class="user-image col">
                    @if (in_array(auth()->user()->avatarType, [ 'image', 'gravatar' ]))
                        <img src="{{ auth()->user()->image }}" alt="" class="circle responsive-img">
                    @else
                        <div class="circle user-initials grey lighten-2">{{ auth()->user()->initials }}</div>
                    @endif
                </div>
                <div class="input-field col">
                    <textarea id="uc-content" class="materialize-textarea"></textarea>
                    <label id="uc-cont-lbl" 
                        for="uc-content"
                        data-trans-new="{{ uctrans('msg.new', $mComment) }}" 
                        data-trans-edit="{{ uctrans('msg.edit', $mComment) }}" 
                        data-trans-reply="{{ uctrans('msg.reply', $mComment) }}" >
                        {{ uctrans('msg.new', $mComment) }}
                    </label>
                    <a href="javascript:void(0)"
                        data-tooltip="{{ uctrans('button.clear', $mComment) }}" 
                        data-position="bottom" 
                        class="clear-btn primary-text">
                        <i class="material-icons">delete</i>
                    </a>
                </div>
                <button class="save-btn btn-floating waves-effect waves-light primary"
                    data-tooltip="{{ uctrans('button.send', $mComment) }}" >
                    <i class="material-icons right">send</i>
                </button>
            </div>
            <div class="uc-content">
                @foreach(Uccello\Comment\Models\Comment::getAll($record, $domain) as $comment)
                @if(!$comment->deleted_at || $comment->replies->count())
                <div class="uc-comment row" id="uc-comment-{{ $comment->id }}" style="margin-bottom: 20px">
                    <div class="user-image col">
                        @if (in_array($comment->user->avatarType, [ 'image', 'gravatar' ]))
                            <img src="{{ $comment->user->image }}" alt="" class="circle responsive-img">
                        @else
                            <div class="circle user-initials grey lighten-2">{{ $comment->user->initials }}</div>
                        @endif
                    </div>
                    <div class="uc-main col">
                        <div class="uc-comment-header row{{$comment->deleted_at ? ' uc-deleted' : ''}}">
                            <div class="col uc-user">
                                @if(auth()->user()->canRetrieve($domain, auth()->user()->module))
                                <a href="{{ucroute('uccello.detail', $domain, 'user', [ 'id' => $comment->user->id ])}}" class="primary-text">
                                    {{ $comment->user->recordLabel }}
                                </a>
                                @else
                                {{ $comment->user->recordLabel }}
                                @endif
                            </div>
                            <div class="col">
                                {{ (new \Carbon\Carbon($comment->created_at))->format(config('uccello.format.php.datetime')) }} 
                            </div>
                            @if (!$comment->deleted_at && $comment->updated_at != $comment->created_at)
                            <div class="col">
                                ({{ uctrans('msg.modified', $mComment) }})
                            </div>
                            @endif
                            @if(!$comment->deleted_at)
                            <div class="col">
                                <a href="javascript:void(0)"
                                data-tooltip="{{ uctrans('button.reply', $mComment) }}"
                                data-position="top"
                                data-comment-id={{ $comment->id }}
                                class="reply-btn primary-text">
                                <i class="material-icons">reply</i>
                                </a>
                                @if (auth()->user()->id == $comment->user->id)
                                @if(!$comment->replies->count() || config('uccello.comment.can_edit_parent', true))
                                <a href="javascript:void(0)"
                                    data-tooltip="{{ uctrans('button.edit', $mComment) }}"
                                    data-position="top"
                                    data-comment-id={{ $comment->id }}
                                    class="edit-btn primary-text">
                                    <i class="material-icons">edit</i>
                                </a>
                                @endif
                                @if(!$comment->replies->count() || config('uccello.comment.can_delete_parent', false))
                                <a href="javascript:void(0)"
                                    data-tooltip="{{ uctrans('button.delete', $mComment) }}" 
                                    data-position="top" 
                                    data-comment-id={{ $comment->id }}
                                    class="delete-btn primary-text">
                                    <i class="material-icons">delete</i>
                                </a>
                                @endif
                                @endif
                            </div>
                            @endif
                        </div>
                        @if($comment->deleted_at)
                        <div class="uc-comment-content uc-deleted">
                            ({{ uctrans('msg.deleted', $mComment) }})
                        </div>
                        @else
                        <div class="uc-comment-content">
                            {!! nl2br($comment->content) !!}
                        </div>
                        @endif
                        @if($comment->replies->count())
                        <a class="uc-toggle-reply btn-flat" data-comment-id={{ $comment->id }} 
                            @if(!config('uccello.comment.show_child', true))style="display:none"@endif>
                            ▴ {{ uctrans('msg.count.hide', $mComment) }} {{$comment->replies->count()}}
                            {{ $comment->replies->count() > 1 ? uctrans('msg.count.replies', $mComment) : uctrans('msg.count.reply', $mComment) }}</a>
                        <a class="uc-toggle-reply btn-flat" data-comment-id={{ $comment->id }} 
                            @if(config('uccello.comment.show_child', true))style="display:none"@endif>
                            ▾ {{ uctrans('msg.count.show', $mComment) }} {{$comment->replies->count()}}
                            {{ $comment->replies->count() > 1 ? uctrans('msg.count.replies', $mComment) : uctrans('msg.count.reply', $mComment) }}</a>
                        <div class="uc-reply uc-toggle-reply" @if(!config('uccello.comment.show_child', true))style="display:none"@endif>
                            @foreach($comment->replies as $reply)
                            <div class="uc-comment row" id="uc-comment-{{ $reply->id }}" style="margin-bottom: 20px">
                                <div class="user-image col">
                                    @if (in_array($reply->user->avatarType, [ 'image', 'gravatar' ]))
                                        <img src="{{ $reply->user->image }}" alt="" class="circle responsive-img">
                                    @else
                                        <div class="circle user-initials grey lighten-2">{{ $reply->user->initials }}</div>
                                    @endif
                                </div>
                                <div class="uc-main col">
                                    <div class="uc-comment-header row">
                                        <div class="col uc-user">
                                            @if(auth()->user()->canRetrieve($domain, auth()->user()->module))
                                            <a href="{{ucroute('uccello.detail', $domain, 'user', [ 'id' => $reply->user->id ])}}" class="primary-text">
                                                {{ $reply->user->recordLabel }}
                                            </a>
                                            @else
                                            {{ $reply->user->recordLabel }}
                                            @endif
                                        </div>
                                        <div class="col">
                                            {{ (new \Carbon\Carbon($reply->created_at))->format(config('uccello.format.php.datetime')) }} 
                                        </div>
                                        @if ($reply->updated_at != $reply->created_at)
                                        <div class="col">
                                            ({{ uctrans('msg.modified', $mComment) }})
                                        </div>
                                        @endif
                                        <div class="col">
                                            @if (auth()->user()->id == $reply->user->id)
                                            <a href="javascript:void(0)"
                                                data-tooltip="{{ uctrans('button.edit', $mComment) }}"
                                                data-position="top"
                                                data-comment-id={{ $reply->id }}
                                                class="edit-btn primary-text">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            @if(!$reply->replies->count())
                                            <a href="javascript:void(0)"
                                                data-tooltip="{{ uctrans('button.delete', $mComment) }}" 
                                                data-position="top" 
                                                data-comment-id={{ $reply->id }}
                                                class="delete-btn primary-text">
                                                <i class="material-icons">delete</i>
                                            </a>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="uc-comment-content">
                                        {!! nl2br($reply->content) !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

@section('extra-meta')
<meta name="save-url" content="{{ ucroute('uccello.comment.save', $domain) }}">
<meta name="delete-url" content="{{ ucroute('uccello.comment.delete', $domain) }}">
<meta name="entity-uuid" content="{{ $record->uuid }}">
<meta name="comment-id" content="">
<meta name="parent-id" content="">
@append

@section('extra-script')
{!! Html::script(mix('js/comment.js', 'vendor/uccello/comment')) !!}

<style>
.uc-comments .row {
    margin-bottom: 0;
}
.uc-comments .uc-user {
    font-weight: bold;
}
.uc-comments a i {
    font-size: 18px;
}
.uc-comments .uc-comment {
    width: 100%;
}
.uc-comments .uc-content {
    overflow-y: auto;
    max-height: {{config('uccello.comment.max_height', 450)}}px;
    padding-top: 20px;
}
.uc-comments .uc-main {
    max-width: calc(100% - 68px);
}
.uc-comments .uc-comment-header {
    width: max-content;
}
.uc-comments .uc-header .input-field {
    margin: 0px;
    width: calc(100% - 130px);
    max-width: 400px;
}
.uc-comments .uc-comment-header {
    width: max-content;
}
.uc-comments .uc-header .clear-btn {
    bottom: 20px;
    right: 10px;
    position: absolute;
}
.uc-comments .uc-deleted {
    color: grey;
    font-style: italic;
}
</style>

@append
