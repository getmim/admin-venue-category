<?php
/**
 * CategoryController
 * @package admin-venue-category
 * @version 0.0.1
 */

namespace AdminVenueCategory\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use AdminSiteMeta\Library\Meta;
use VenueCategory\Model\{
    VenueCategory as VCategory,
    VenueCategoryChain as VCChain
};

class CategoryController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['venue', 'category']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_category)
            return $this->show404();

        $category = (object)[];

        $id = $this->req->param->id;
        if($id){
            $category = VCategory::getOne(['id'=>$id]);
            if(!$category)
                return $this->show404();
            Meta::parse($category, 'meta');
            $params = $this->getParams('Edit Venue Category');
        }else{
            $params = $this->getParams('Create New Venue Category');
        }

        $form           = new Form('admin.venue-category.edit');
        $params['form'] = $form;

        if(!($valid = $form->validate($category)) || !$form->csrfTest('noob'))
            return $this->resp('venue/category/edit', $params);

        Meta::combine($valid, 'meta');

        if($id){
            if(!VCategory::set((array)$valid, ['id'=>$id]))
                deb(VCategory::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!VCategory::create((array)$valid))
                deb(VCategory::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'venue-category',
            'original' => $category,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminVenueCategory');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_category)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $categories = VCategory::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($categories)
            $categories = Formatter::formatMany('venue-category', $categories, ['user']);

        $params               = $this->getParams('Venue Category');
        $params['categories'] = $categories;
        $params['form']       = new Form('admin.venue-category.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = VCategory::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminVenueCategory'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('venue/category/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_category)
            return $this->show404();

        $id       = $this->req->param->id;
        $category = VCategory::getOne(['id'=>$id]);
        $next     = $this->router->to('adminVenueCategory');
        $form     = new Form('admin.venue-category.index');

        if(!$category)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'venue-category',
            'original' => $category,
            'changes'  => null
        ]);

        VCategory::remove(['id'=>$id]);
        VCChain::remove(['category'=>$id]);
        
        $this->res->redirect($next);
    }
}