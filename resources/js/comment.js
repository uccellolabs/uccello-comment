export class Comment {
    constructor() {
        this.isSent = false;
        this.initListeners();
    }

    /**
     * Init event listeners
     */
    initListeners() {
        this.initSaveButtonListener();
        this.initClearButtonListener();
        this.initEditButtonsListener();
        this.initReplyButtonsListener();
        this.initDeleteButtonListener();
        this.toggleReplyButtonsListener();
    }

    /**
     * Save current menu
     */
    save() {
        let url     = $("meta[name='save-url']").attr('content');
        let entity  = $("meta[name='entity-uuid']").attr('content');
        let parent   = $("meta[name='parent-id']").attr('content');
        let id      = $("meta[name='comment-id']").attr('content');
        let content = $(".uc-comments #uc-content").val();

        if (content != null && content != '' && !this.isSent) {
            this.isSent = true;
            $.ajax({
                url: url,
                method: "post",
                data: {
                    _token: $("meta[name='csrf-token']").attr('content'),
                    entity: entity,
                    parent: parent,
                    id: id,
                    content: content,
                }
            }).then((response) => {
                $(".uc-comments #uc-content").val("");
                $(".uc-comments #uc-content").trigger('autoresize'); // TODO: Not working =/
                location.reload();
            }).fail((error) => {
                swal(uctrans.trans('uccello::default.dialog.error.title'), uctrans.trans('uccello::settings.menu_manager.error.save'), "error")
            })
        }
    }

    /**
     * Save current menu
     */
    delete(id) {
        let url = $("meta[name='delete-url']").attr('content');

        if (id != null && id != '') {
            $.ajax({
                url: url,
                method: "post",
                data: {
                    _token: $("meta[name='csrf-token']").attr('content'),
                    id: id,
                }
            }).then((response) => {
                location.reload();
            }).fail((error) => {
                swal(uctrans.trans('uccello::default.dialog.error.title'), uctrans.trans('uccello::settings.menu_manager.error.save'), "error")
            })
        }
    }
    
    /**
     * Init save button listener
     */
    initSaveButtonListener() {
        $('.uc-comments .save-btn').on('click', (event) => {
            // Save comment
            this.save();
        })
    }

    /**
         * Init clear button listener
         */
    initClearButtonListener() {
        $('.uc-comments .clear-btn').on('click', (event) => {
            this.clear();

            $(".uc-comments #uc-content").trigger('autoresize'); // TODO: Not working =/
            $(".uc-comments #uc-content").focus();
        })
    }

    /**
     * Init save button listener
     */
    initDeleteButtonListener() {
        $('.uc-comments .delete-btn').on('click', (event) => {
            var commentId = $(event.currentTarget).data('commentId');

            swal({
                title: uctrans.trans('uccello::default.confirm.dialog.title'),
                text: uctrans.trans('uccello::default.confirm.button.delete_record'),
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: [
                    uctrans.trans('uccello::default.button.no'),
                    uctrans.trans('uccello::default.button.yes')
                ],
            })
            .then((response) => {
                if (response === true) {
                    // Delete comment
                    this.delete(commentId);
                }
            })
        })
    }

    /**
     * Init edit buttons listener
     */
    initEditButtonsListener() {
        $('.uc-comments .edit-btn').on('click', (event) => {
            this.clear();
            
            var commentId = $(event.currentTarget).data('commentId');
            var content = $("#uc-comment-" + commentId + " .uc-comment-content").first().text().trim();

            $("meta[name='comment-id']").attr('content', commentId);
            $(".uc-comments #uc-content").val(content);
            $(".uc-comments #uc-content").trigger('autoresize'); // TODO: Not working =/
            $(".uc-comments #uc-content").focus();

            var trans = $(".uc-comments #uc-cont-lbl").data('trans-edit');
            $(".uc-comments #uc-cont-lbl").text(trans);
        })
    }
    
    /**
     * Init reply buttons listener
     */
    initReplyButtonsListener() {
        $('.uc-comments .reply-btn').on('click', (event) => {
            this.clear();
            
            var replyId = $(event.currentTarget).data('commentId');
            var user = $("#uc-comment-" + replyId + " .uc-user").first().text().trim();
            
            $("meta[name='parent-id']").attr('content', replyId);
            $(".uc-comments #uc-content").focus();

            var trans = $(".uc-comments #uc-cont-lbl").data('trans-reply');
            $(".uc-comments #uc-cont-lbl").text(trans + " @" + user);
        })
    }
    
    /**
     * Toggle reply buttons listener
     */
    toggleReplyButtonsListener() {
        $('.uc-comments .uc-toggle-reply').on('click', (event) => {
            var replyId = $(event.currentTarget).data('commentId');

            $("#uc-comment-" + replyId + " .uc-toggle-reply").toggle();
        })
    }

    /**
     * Clear comment edit meta
     */
    clear()
    {               
        $("meta[name='comment-id']").attr('content', "");
        $("meta[name='parent-id']").attr('content', "");

        $(".uc-comments #uc-content").val("");
        $(".uc-comments #uc-content").trigger('autoresize'); // TODO: Not working =/
        
        var trans = $(".uc-comments #uc-cont-lbl").data('trans-new');
        $(".uc-comments #uc-cont-lbl").text(trans);
    }
}

new Comment();
