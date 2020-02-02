<?php

namespace Uccello\Comment\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Uccello\Core\Models\Module;

class CommentWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $relatedModule = ucmodule($this->config['module']);
        $record = ucrecord($this->config['record_id'], $relatedModule->model_class);

        return view('comment::widgets.comment_widget', [
            'config' => $this->config,
            'record' => $record,
            'mComment' => Module::where('name', 'comment')->first(),
        ]);
    }
}
