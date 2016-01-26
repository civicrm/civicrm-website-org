<?php

/**
 * @file
 * Template for a 3 column panel layout.
 *
 * This template provides a three column panel display layout, with
 * each column roughly equal in width.
 *
 * Variables:
 * - $id: An optional CSS id to use for the layout.
 * - $content: An array of content, each item in the array is keyed to one
 *   panel of the layout. This layout supports the following sections:
 *   - $content['left']: Content in the left column.
 *   - $content['middle']: Content in the middle column.
 *   - $content['right']: Content in the right column.
 */
foreach ($content as $p => $c) {
    //print "<div> {$c} </div>";
    if ($c) {
        if ($p == 'banner') {
            print "<div class=\"stripe stripe-{$p}\" id=\"{$p}\"> {$c} </div>";
        } else {
            print "<div class=\"stripe stripe-{$p}\" id=\"{$p}\"> <div class=\"container\"> <div class=\"row\"> {$c} </div> </div> </div>";
        }
    }
}
