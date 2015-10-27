<?php

class LinkPager extends CLinkPager{
    protected function createPageButtons()
    {
        if(($pageCount = $this->getPageCount())<=1) 
            return array();

        list($beginPage,$endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false);
        $buttons = array();

        // prev page
        if(($page = $currentPage-1) < 0)
           $page = 0;
        if($page > 0)
           $buttons[] = $this->createPageButton($this->prevPageLabel,$page,self::CSS_PREVIOUS_PAGE,$currentPage<=0,false);
        
        // internal pages
        for($i = $beginPage; $i <= $endPage; ++$i) {
            if($i == $beginPage && $i > 0) {
                // first page
                $buttons[] = $this->createPageButton(0+1,0,self::CSS_FIRST_PAGE,$currentPage<=0,false);
                $buttons[] = '<li class="'.$this->internalPageCssClass.'">...</li>';
            }
                
            $buttons[] = $this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);
            
            if($i == $endPage && ($i+1) != $this->getPageCount()) {
                $buttons[] = '<li class="'.$this->internalPageCssClass.' '.$i.' '.$endPage.'">...</li>';
                // last page
                $buttons[] = $this->createPageButton($this->getPageCount(),$pageCount-1,self::CSS_LAST_PAGE,$currentPage>=$pageCount-1,false);
            }
        }
        
        // next page
        if(($page = $currentPage+1) >= $pageCount-1)
            $page = $pageCount-1;
        if(($currentPage+1) < $this->getPageCount())
           $buttons[] = $this->createPageButton($this->nextPageLabel,$page,self::CSS_NEXT_PAGE,$currentPage>=$pageCount-1,false);

        return $buttons;
    }
}