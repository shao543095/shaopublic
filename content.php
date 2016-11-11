<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author		shaopengcheng
 * @data		2016-08-23
 * @category	Content
 * @copyright	Copyright(c)
 * @version     $Id:$
 */
class Content extends CI_controller // todowhy 注意Class名称的大小写问题
{
    /**
     * 飞到门-内容管理
     *
     * @access    public
     *
     * @return    void
     */
    public function index()
    {
        $params = $this->input->get();
        data_filter($params);

        // 附加数据
        $params['is_pages'] = true;
        $params['order_by'] = 'content_id desc';

        // todowhy 为什么我要这么写？
        $result = [
            'total' => 0,
            'list'  => [],
        ];
        // todowhy 注意load model时的大小写
        $this->load->model('Content_model');
        if (isset($params['select'])) {
            $result = $this->Content_model->get_list($params);
        }

        $this->load->library('pagination');
        $data = [
            'search'        => $params,
            'list'          => $result['list'],
            'pagination'    => $this->pagination->get_page_bar($result['total']),
        ];

        $this->load->view('base/head.tpl');
        $this->load->view('content_a/index.tpl', $data);
        $this->load->view('base/foot.tpl');
    }

    /**
     * 删除内容
     *
     * @access    public
     *
     * @return    void
     */
    public function delete()
    {
        $params = $this->input->get();
        data_filter($params);

        // todowhy 如果数据之前就已经被删除了怎么办？
        $this->load->model('Content_model');
        $params['is_deleted'] = 1;
        $result = $this->Content_model->edit($params);

        if (false === $result) {
            show_msg($this->Content_model->get_error(), 'javascript:history.back();');
        }   else {
            url_redirect(BKD_DOMAIN.'content');
        }
    }

    /**
     * 添加内容
     *
     * @access    public
     *
     * @return    void
     */
    public function add()
    {
        if ($this->input->is_post_request()) {
            $params = $this->input->post();

            // todowhy 为什么要写成 no ? is_filter 有什么用?
            $params['is_filter'] = no;

            $this->load->model('Content_model');
            $result = $this->Content_model->add_content($params);
// todowhy 为什么提交代码前要遗留这样的代码？
// dump($result);exit;
            if (false === $result) {
                json_exit($this->Content_model->get_error());
            } else {
                json_exit('添加成功!', true);
            }
        } else {
            $params = $this->input->get();

            $data = [
                'search' => $params,
            ];

            $this->load->view('base/head.tpl');
            $this->load->view('content_a/add_content.tpl', $data);
            $this->load->view('base/foot.tpl');
        }
    }

    /**
     * 内容编辑
     *
     * @access    public
     *
     * @return    void
     */
    public function edit()
    {
        $this->load->model('Content_model');

        if ($this->input->is_post_request()) {
            $params = $this->input->post();
            data_filter($params);
            $params['is_filter'] = 1;

            // 注意,右边要有空格.的两边都要有空格
            $result = $this->Content_model->edit_content($params, $params['content_id']);
            if (false === $result) {
                json_exit($this->Content_model->get_error());
            } else {
                json_exit('操作成功!', true);
            }
        } else {
            $params = $this->input->get();
            data_filter($params);
// 同样的问题
//dump($params);exit;
            $result = $this->Content_model->get_list_by_id($params);
            $data = [
                'search' => $result,
            ];

            $this->load->view('base/head.tpl');
            $this->load->view('content_a/content_edit.tpl', $data);
            $this->load->view('base/foot.tpl');
        }
    }

    /**
     * 首页详情
     *
     * @access    public
     *
     * @return    void
     */
    public function detail()
    {
        $params = $this->input->get();
        data_filter($params);

        $this->load->model('Content_model');
        $result = $this->Content_model->get_list_by_id($params);

        $data = [
            'search' => $result,
        ];

        $this->load->view('base/head.tpl');
        $this->load->view('content_a/content_detail.tpl', $data);
        $this->load->view('base/foot.tpl');
    }

    /**
     * 编辑有效无效
     *
     * @access    public
     *
     * @return    void
     */
    public function enabled()
    {
        if ($this->input->is_post_request()) {
            $params = $this->input->post();
            data_filter($params);

            $this->load->model('Content_model');
            $result = $this->Content_model->edit_by_id($params, $params['content_id']);

            if (false === $result) {
                json_exit($this->Content_model->get_error());
            } else {
                json_exit('操作成功!', true);
            }
        } else { // todowhy 为什么要加这一段？
            json_exit('非法操作!', true);
        }
    }

}