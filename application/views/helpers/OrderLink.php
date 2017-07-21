<?
class Zend_View_Helper_OrderLink extends Zend_View_Helper_Abstract
{

	public function orderLink( $data )
    {
    	$img_asc= ($this->view->filed == $data['filed'] && $this->view->order_t == "ASC" ) ? "sort_asc_on" : "sort_asc";
    	$img_desc= ($this->view->filed == $data['filed'] && $this->view->order_t == "DESC" ) ? "sort_desc_on" : "sort_desc";
    	$str= "<span class='tosort'>".$this->view->Translate($data['title'])."</span>
	           <div class='sorting'>
	           		<a href='{$this->view->url( array('filed'=>$data['filed'], 'order_t'=>'ASC') ) }' class='$img_asc'></a>
	           		<a href='{$this->view->url( array('filed'=>$data['filed'], 'order_t'=>'DESC') ) }' class='$img_desc'></a>
	           </div>"; 
        return $str;
    }
    
}
