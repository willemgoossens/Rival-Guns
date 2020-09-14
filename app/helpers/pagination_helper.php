<?php
    /**
     * 
     * 
     * Paginate
     * @param Int currentPage
     * @param Int casesPerPage
     * @param Int totalCases
     * @param String pageURL
     * @return String
     * 
     * 
     */
    function paginate(int $currentPage, int $casesPerPage, int $totalCases, String $pageURL): String
    {
        // First calculate the total amount of pages
        $totalPages = ceil($totalCases / $casesPerPage);

        // Start of the pagination thingy
        $html = "<nav aria-label=\"...\">
                <ul class=\"pagination justify-content-center\">";

        if($currentPage == 1)
        {
            $html .= "<li class=\"page-item disabled\">
                        <span class=\"page-link\">&laquo;</span>
                      </li>";
        }
        else
        {
            $html .= "<li class=\"page-item\">
                        <a class=\"page-link\" href=\"" . $pageURL . "/" . ($currentPage - 1) . "\">&laquo;</a>
                      </li>";
        }

        if($currentPage - 5 > 1)
        {
            $html .= "<li class=\"page-item\">
                          <a class=\"page-link\" href=\"" . $pageURL . "/1\">1</a>
                      </li>
                      <li class=\"page-item disabled\">
                          <span class=\"page-link\">...</span>
                      </li>";
        }
        
        for($i = $currentPage - 5; $i < $currentPage; $i++)
        {
            // If the page is below 0, don't show
            // Otherwise you should
            if($i > 0)
            {
                $html .= "<li class=\"page-item\">
                              <a class=\"page-link\" href=\"" . $pageURL . "/" . $i . " \">" . $i . "</a>
                          </li>";
            }
        }

        $html .= "<li class=\"page-item active\">
                      <span class=\"page-link\">
                          " . $currentPage . "
                          <span class=\"sr-only\">(current)</span>
                      </span>
                  </li>";

                  
        for($i = $currentPage + 1; $i <= $currentPage + 5; $i++)
        {
            if($i <= $totalPages)
            {
                $html .= "<li class=\"page-item\">
                              <a class=\"page-link\" href=\"" . $pageURL . "/" . $i . " \">" . $i . "</a>
                          </li>";
            }
        }

        // If there are plenty of pages
        // We might want to show a link to the last page as well
        if($totalPages - $currentPage > 5)
        {
            $html .= "
                      <li class=\"page-item disabled\">
                          <span class=\"page-link\">...</span>
                      </li>
                      <li class=\"page-item\">
                          <a class=\"page-link\" href=\"" . $pageURL . "/" . $totalPages . " \">" . $totalPages . "</a>
                      </li>";
        }

        // Now create the Next button
        // Either enabled or disabled
        if($currentPage == $totalPages)
        {
            $html .= "<li class=\"page-item disabled\">
                            <span class=\"page-link\">&raquo;</span>
                      </li>";
        }
        else
        {
            $html .= "<li class=\"page-item\">
                          <a class=\"page-link\" href=\"" . $pageURL . "/" . ($currentPage + 1) . "\">&raquo;</a>
                      </li>";
        }

        $html .= "  </ul>
              </nav>";

        // And return html
        return $html;
    }