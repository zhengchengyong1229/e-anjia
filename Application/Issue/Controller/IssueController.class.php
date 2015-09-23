<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class IssueController extends AdminController
{
    protected $issueModel;

    function _initialize()
    {
        //将模型放入属性中
        $this->issueModel = D('Issue/Issue');
        //继承父类的构造函数
        parent::_initialize();
    }
   
    /*
     * 从数据库中获取配置信息，并将其通过ConfigBuilder展示，并可以重新定义
     * display  在各个builder中调用
     */
    public function config()
    {
        //use 了这个类，就可以直接实例化
        $admin_config = new AdminConfigBuilder();
        //获取本控制器的配置文件,handleConfit 可赋值,可提取
        //当按button提交回本页面是handleConfig来处理post数据
        //代码太美丽了
        $data = $admin_config->handleConfig();

        //设置页面title
                     //同页面title和内部标题
        $admin_config->title('专辑基本设置')
                     //二选一,radio true (1)  false(0)
                     ->keyBool('NEED_VERIFY', '投稿是否需要审核','默认无需审核')
                     ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }

    //专辑管理页面
    //树状list,正是我需要研究的部分
    //分类操作常和TREE一起使用
    public function issue()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();

        //看上去是form 中的参数，但是并没有使用，不解
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $attr1 = $attr;
        $attr1['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 1));
        $attr0 = $attr;
        $attr0['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 0));
        //不理解啊，不理解


        $tree = D('Issue/Issue')->getTree(0, 'id,title,sort,pid,status');

        $builder->title('专辑管理')
                ->buttonNew(U('Issue/add'))
                ->data($tree)
                ->display();
    }

    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $issue = $this->issueModel->create();
                if ($this->issueModel->save($issue)) {

                    $this->success('编辑成功。');
                } else {
                    $this->error('编辑失败。');
                }
            } else {
                $issue = $this->issueModel->create();
                if ($this->issueModel->add($issue)) {

                    $this->success('新增成功。');
                } else {
                    $this->error('新增失败。');
                }
            }


        } else {
            $builder = new AdminConfigBuilder();
            $issues = $this->issueModel->select();
            $opt = array();
            foreach ($issues as $issue) {
                $opt[$issue['id']] = $issue['title'];
            }
            if ($id != 0) {
                $issue = $this->issueModel->find($id);
            } else {
                $issue = array('pid' => $pid, 'status' => 1);
            }


            $builder->title('新增分类')->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类')+$opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($issue)
                ->buttonSubmit(U('Issue/add'))->buttonBack()->display();
        }

    }

    //专辑回收站处理
    //你妹的，这特么也太贴心了吧，连回收站的按钮都给定义好了，很方便，要求严格遵守官方的编码规范才可以，可拓展性待定
    public function issueTrash($page = 1, $r = 20,$model='')
    {
        $builder = new AdminListBuilder();
        //clearTrash这个方法是用来处理数据的，在展示页面时并不起作用，当post发生时才起作用
        //所以只要post该方法就会触发清空操作，这真的安全么,一定要确保管理员账号安全，不然任何人都可以清除数据库中含有status ＝－1 
        //的数据，太他妈恐怖了
        $builder->clearTrash($model);
        //读取垃圾信息列表
        
        $map = array('status' => -1);
        $model = $this->issueModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder->title('专辑回收站')
             //设置setStautusUrl 的 server
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('Issue/Issue')
            ->keyId()->keyText('title', '标题')->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('Issue')->find($from);

        $opt = array();
        $issues = $this->issueModel->select();
        foreach ($issues as $issue) {
            $opt[$issue['id']] = $issue['title'];
        }
        if ($type === 'move') {

            $builder->title('移动分类')->keyId()->keySelect('pid', '父分类', '选择父分类', $opt)->buttonSubmit(U('Issue/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title('合并分类')->keyId()->keySelect('toid', '合并至的分类', '选择合并至的分类', $opt)->buttonSubmit(U('Issue/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count=D('IssueContent')->where(array('issue_id'=>$id))->setField('issue_id',$toid);
        D('Issue')->where(array('id'=>$id))->setField('status',-1);
        $this->success('合并分类成功。共影响了'.$effect_count.'个内容。',U('issue'));
        //TODO 实现合并功能 issue
    }

    //此方法为专辑模块默认主页
    //使用ListBuiler构造  : 表格  List 页面通常与分页配合使用$page 第几页,$r 每个页面几条数据
    public function contents($page=1,$r=10){
        //读取列表
        $map = array('status' => 1);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);//TODO   这个地方不明变变量从何处来,删除li变量
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();

        //此属性没有使用
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title('内容管理')
            //页面顶部两个按钮
            //setStatusUrl 为有关状态操作按钮指定server
            ->setStatusUrl(U('setIssueContentStatus'))->buttonDisable('','审核不通过')->buttonDelete()
            //表格格式安排
            ->keyId()->keyLink('title', '标题','Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            //数据,问题，顺序要特别指定么？
            ->data($list)
            //展示分页
            ->pagination($totalCount, $r)
            //展示
            ->display();
    }
    //审核页面，之前经纪人我一直脚verify 这到底时他妈为什么，审核，审核，审核
    /*
     *流程先从数据库获取data数据
     *使用builder方法构建页面
     *
     */
    public function verify($page=1,$r=10){
        //读取列表
        $map = array('status' => 0);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        /*
         *
         *  又unset($li)
         *  Why
         */
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();

        //又出现未曾使用的变量，这到底时为什么啊
        /*
         * 研究form 属性时如何定义的
         */
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        //这个布局和页面展示的顺序一样，这个习惯很好
        $builder->title('审核内容')
                ->setStatusUrl(U('setIssueContentStatus'))->buttonEnable('','审核通过')->buttonDelete()
                ->keyId()->keyLink('title', '标题','Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
                ->data($list)
                ->pagination($totalCount, $r)
                ->display();
    }

    //这个方法的名字时严格定义的用set＋MODULE_NAME+Status来处理
    public function setIssueContentStatus(){
        $ids=I('ids');
        $status=I('get.status',0,'intval');
        $builder = new AdminListBuilder();
        if($status==1){
            foreach($ids as $id){
                $content=D('IssueContent')->find($id);
                //发送站内消息
                D('Common/Message')->sendMessage($content['uid'],"管理员审核通过了您发布的内容。现在可以在列表看到该内容了。" , $title = '专辑内容审核通知', U('Issue/Index/issueContentDetail',array('id'=>$id)), is_login(), 2);
               /*同步微博*/
              /*  $user = query_user(array('nickname', 'space_link'), $content['uid']);
                $weibo_content = '管理员审核通过了@' . $user['nickname'] . ' 的内容：【' . $content['title'] . '】，快去看看吧：' ."http://$_SERVER[HTTP_HOST]" .U('Issue/Index/issueContentDetail',array('id'=>$content['id']));
                $model = D('Weibo/Weibo');
                $model->addWeibo(is_login(), $weibo_content);*/
                /*同步微博end*/
            }
        }
        $builder->doSetStatus('IssueContent', $ids, $status);
    }

    public function contentTrash($page=1, $r=10,$model=''){
        //读取微博列表
        $builder = new AdminListBuilder();
        //内含  delete 方法
        $builder->clearTrash($model);
        $map = array('status' => -1);
        $model = D('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title('内容回收站')
            ->setStatusUrl(U('setIssueContentStatus'))->buttonRestore()->buttonClear('IssueContent')
            ->keyId()->keyLink('title', '标题','Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
}
