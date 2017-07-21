<?
class Zend_View_Helper_Message extends Zend_View_Helper_Abstract
{

	public function message( $string="" )
    {
        return $string ? $this->view->Translate( $string ) : 
               ( $this->view->message ? $this->view->Translate( $this->view->message ) : "" );
    }
    
}
