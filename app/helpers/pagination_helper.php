<?php
  // This helper is meant to create the pagination for a list
  function paginate($currentPage, $casesPerPage, $totalCases, $pageURL){
    // First calculate the total amount of pages
    $totalPages = ceil($totalCases / $casesPerPage);
    // Start of the pagination thingy
    $html = "<nav aria-label=\"...\">
            <ul class=\"pagination justify-content-center\">";
    // Create the back button
    // Either disabled, if you're on the first page
    // Or enabled if this is not the case
    if($currentPage == 1){
      $html .= "<li class=\"page-item disabled\">
              <span class=\"page-link\">&laquo;</span>
            </li>";
    }else{
      $html .= "<li class=\"page-item\">
              <a class=\"page-link\" href=\"" . $pageURL . "/" . ($currentPage - 1) . "\">&laquo;</a>
            </li>";
    }

    // Now add the middle part
    // If there are plenty of pages
    // We might want to show a link to the first one as well
    if($currentPage - 5 > 1){
      $html .= "<li class=\"page-item\">
                  <a class=\"page-link\" href=\"" . $pageURL . "/1\">1</a>
                </li>
                <li class=\"page-item disabled\">
                  <span class=\"page-link\">...</span>
                </li>";
    }
    // Show a link to the previous 5 pages
    for($i = $currentPage - 5; $i < $currentPage; $i++){
      // If the page is below 0, don't show
      // Otherwise you should
      if($i > 0){
        $html .= "<li class=\"page-item\">
                    <a class=\"page-link\" href=\"" . $pageURL . "/" . $i . " \">" . $i . "</a>
                  </li>";
      }
    }
    // Now show the current page
    $html .= "<li class=\"page-item active\">
                <span class=\"page-link\">
                  " . $currentPage . "
                  <span class=\"sr-only\">(current)</span>
                </span>
              </li>";

    // Show a link to the next 5 pages
    for($i = $currentPage + 1; $i <= $currentPage + 5; $i++){
      // If the page is below 0, don't show
      // Otherwise you should
      if($i <= $totalPages){
        $html .= "<li class=\"page-item\">
                    <a class=\"page-link\" href=\"" . $pageURL . "/" . $i . " \">" . $i . "</a>
                  </li>";
      }
    }
    // If there are plenty of pages
    // We might want to show a link to the last page as well
    if($totalPages - $currentPage > 5){
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
    if($currentPage == $totalPages){
      $html .= "<li class=\"page-item disabled\">
                  <span class=\"page-link\">&raquo;</span>
                </li>";
    }else{
      $html .= "<li class=\"page-item\">
                  <a class=\"page-link\" href=\"" . $pageURL . "/" . ($currentPage + 1) . "\">&raquo;</a>
                </li>";
    }

    // Now close the pagination part
    $html .= "  </ul>
          </nav>";

    // And return html
    return $html;
  }

 ?>
