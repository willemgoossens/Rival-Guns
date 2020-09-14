<?php
    require_once ROOT . '/vendor/autoload.php';
    // This helper uses Quill
    // Check out Quill https://quilljs.com/
    $quill = [];
    $quillForm = "";

    /**
    *
    *
    * set The editor Form for Quill
    * @param String formId
    * @return Void
    *
    *
    */
    function setQuillFormId(string $formId): void
    {
        global $quillForm;
        $quillForm = $formId;
    }

    /**
     * 
     * 
     * add an element id to the array of Quill elements
     * @param String ElemId [Element Id]
     * @param String outputName, [the name that's eventually used in the input field]
     * @param Void
     * 
     * 
     */
    function setQuillAddField(string $elemId, string $outputName = null): void
    {
        global $quill;

        if(! isset($outputName))
        {
            $outputName = $elemId;
        }

        array_push($quill, [$elemId, $outputName]);
    }

    /**
    *
    *
    * run the editor
    *
    *
    */
    function runQuillEditor()
    {
        global $quill, $quillForm;
        // Call the script and run the code
        if(! empty($quillForm))
        {
            echo "
                <link href=\"https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.snow.min.css\" rel=\"stylesheet\">
                <script src='https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js'></script>
                <script>
                  // Use this binding
                  // Quill doesn't work for properly nested lists
                  // So these are disabled
                  var bindings = {
                                  \"indent\": {
                                    key: \"tab\",
                                    format: [\"list\"],
                                    handler: function(range, context) {
                                      return false;
                                    }
                                  }
                                };
                                var extraVars = new Array();
                                var quills = new Array();
                                ";

            foreach($quill as $elem)
            {
                echo "quills['" . $elem[0] . "'] = new Quill('#" . $elem[0] . "',
                                  {
                                    theme: 'snow',
                                    modules: {keyboard: {
                                                          bindings: bindings
                                                        },
                                                        toolbar: [
                                                          [
                                                            {
                                                              'header': [1, 2, 3, false]
                                                            }
                                                          ],
                                                          ['bold', 'italic'],
                                                          [
                                                            {'list': 'ordered'},
                                                            {'list':'bullet'}
                                                          ],
                                                          ['link', 'clean']
                                                        ]
                                                      }
                                  });";
            }

            echo "$('#" . $quillForm . "').on('submit',function() {";

            foreach($quill as $elem) 
            {
                echo "$('#" . $quillForm . "').append('<input type=\"text\" name=\"" . $elem[1] . "\" value=\"' + quills['" . $elem[0] . "'].root.innerHTML + '\" class=\"d-none\" />');";
            }

            echo "
                });
                </script>";
        }
    }


    /**
     * 
     * 
     * MarkdownToHtml
     * @param String Markdown
     * @return String
     * 
     * 
     */
    // https://github.com/erusev/parsedown
    function MarkdownToHTML(String $markdown): String
    {
        $Parsedown = new \Parsedown();
        $Parsedown->setSafeMode(true);
        return $Parsedown->text($markdown);
    }
    
    
    /**
     * 
     * 
     * HTMLToMarkdown
     * @param String html
     * @return String
     * 
     * 
     */
    // https://github.com/thephpleague/html-to-markdown
    use League\HTMLToMarkdown\HtmlConverter;
    function HTMLToMarkdown(string $html): String
    {
        $converter = new HtmlConverter();
        return $converter->convert($html);
    }

    /**
     * 
     * 
     * closeTags [A function used to add incompleted tags]
     * @param String html
     * @return String html
     */
    //  A function used to add incompleted tags
    function closeTags(string $html): String
    {
        // It is possible that a html tag has been cut off by the shortener...
        // Check if the last < comes behind the last >
        if(mb_strrpos($html, "<") > mb_strrpos($html, ">"))
        {
            // Check for the location and remove this part from $html
            $html = substr($html, 0 , mb_strrpos($html, "<") - 1);
        }
        
        // Now we can continue
        // Index all the opened tags
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedTags = $result[1];
        // Index all the closed tags
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedTags = $result[1];
        $lenOpened = count($openedTags);
        if( count($closedTags) == $lenOpened) 
        {
            return $html;
        }

        // Reverse the list of opened tags
        // Check if they have been closed
        // Otherwise close them
        $openedTags = array_reverse($openedTags);
        for ($i=0; $i < $lenOpened; $i++) 
        {
            if (!in_array($openedTags[$i], $closedTags))
            {
                $html .= '</'.$openedTags[$i].'>';
            } 
            else 
            {
                unset($closedTags[array_search($openedTags[$i], $closedTags)]);
            }
        }
        // Return the html
        return $html;
    }
