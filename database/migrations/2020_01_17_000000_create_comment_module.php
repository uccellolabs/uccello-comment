<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Uccello\Core\Database\Migrations\Migration;
use Uccello\Core\Models\Module;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Tab;
use Uccello\Core\Models\Block;
use Uccello\Core\Models\Field;
use Uccello\Core\Models\Filter;
use Uccello\Core\Models\Relatedlist;
use Uccello\Core\Models\Link;
use Uccello\Core\Models\Widget;

class CreateCommentModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createTable();
        $module = $this->createModule();
        $this->activateModuleOnDomains($module);
        $this->createTabsBlocksFields($module);
        $this->createFilters($module);
        $this->createRelatedLists($module);
        $this->createLinks($module);
        $this->createWidgetEntry($module);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop table
        Schema::dropIfExists($this->tablePrefix . 'comments');

        // Delete module
        Module::where('name', 'comments')->forceDelete();

        Widget::where('class', 'Uccello\Comment\Widgets\CommentWidget')->forceDelete();
    }

    protected function initTablePrefix()
    {
        $this->tablePrefix = 'uccello_';

        return $this->tablePrefix;
    }

    protected function createTable()
    {
        Schema::create($this->tablePrefix . 'comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->string('entity_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('entity_id')->references('id')->on(env('UCCELLO_TABLE_PREFIX', 'uccello_') . 'entities');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('parent_id')->references('id')->on(env('UCCELLO_TABLE_PREFIX', 'uccello_') . 'comments');
        });
    }

    protected function createModule()
    {
        $module = new Module([
            'name' => 'comment',
            'icon' => 'textsms',
            'model_class' => 'Uccello\Comment\Models\Comment',
            'data' => ['package' => 'uccello/comment', 'admin' => true]
        ]);
        $module->save();
        return $module;
    }

    protected function activateModuleOnDomains($module)
    {
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $domain->modules()->attach($module);
        }
    }

    protected function createTabsBlocksFields($module)
    {
        // Tab tab.main
        $tab = Tab::create([
            'module_id' => $module->id,
            'label' => 'tab.main',
            'icon' => null,
            'sequence' => $module->tabs()->count(),
            'data' => null
        ]);

        // Block block.general
        $block = Block::create([
            'module_id' => $module->id,
            'tab_id' => $tab->id,
            'label' => 'block.general',
            'icon' => 'info',
            'sequence' => $tab->blocks()->count(),
            'data' => null
        ]);

        // Field content
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'content',
            'uitype_id' => uitype('textarea')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => $block->fields()->count(),
            'data' => ['rules' => 'required']
        ]);

        // Field entity
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'entity',
            'uitype_id' => uitype('text')->id,
            'displaytype_id' => displaytype('detail')->id,
            'sequence' => $block->fields()->count(),
            'data' => null
        ]);

        // Field user
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'user',
            'uitype_id' => uitype('entity')->id,
            'displaytype_id' => displaytype('create_detail')->id,
            'sequence' => $block->fields()->count(),
            'data' => ['rules' => 'required', 'module' => 'user']
        ]);

        // Field parent
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'parent',
            'uitype_id' => uitype('entity')->id,
            'displaytype_id' => displaytype('everywhere')->id,
            'sequence' => $block->fields()->count(),
            'data' => ['module' => 'comment']
        ]);

        // Field created_at
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'created_at',
            'uitype_id' => uitype('date')->id,
            'displaytype_id' => displaytype('detail')->id,
            'sequence' => $block->fields()->count(),
            'data' => null
        ]);

        // Field updated_at
        Field::create([
            'module_id' => $module->id,
            'block_id' => $block->id,
            'name' => 'updated_at',
            'uitype_id' => uitype('date')->id,
            'displaytype_id' => displaytype('detail')->id,
            'sequence' => $block->fields()->count(),
            'data' => null
        ]);
    }

    protected function createFilters($module)
    {
        // Filter
        $filter = new Filter([
            'module_id' => $module->id,
            'domain_id' => null,
            'user_id' => null,
            'name' => 'filter.all',
            'type' => 'list',
            'columns' => ['content', 'user', 'parent'],
            'conditions' => null,
            'order' => null,
            'is_default' => true,
            'is_public' => false
        ]);
        $filter->save();

    }

    protected function createRelatedLists($module)
    {
    }

    protected function createLinks($module)
    {
    }

    protected function createWidgetEntry($module)
    {
        Widget::create([
            'label' => 'widget.comments',
            'type' => 'summary',
            'class' => 'Uccello\Comment\Widgets\CommentWidget',
            'data' => ['package' => 'uccello/comment']
        ]);
    }
}
