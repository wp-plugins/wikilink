<?php
  function abstractE($text, $num = 500)
  {
      if (preg_match_all('/\s+/', $text, $junk) <= $num)
          return $text;
      $text = preg_replace_callback('/(<\/?[^>]+\s+[^>]*>)/', '_abstractProtect', $text);
      $words = 0;
      $out = array();
      $stack = array();
      $tok = strtok($text, "\n\t ");
      while ($tok !== false and strlen($tok))
      {
          if (preg_match_all('/<(\/?[^\x01>]+)([^>]*)>/', $tok, $matches, PREG_SET_ORDER))
          {
              foreach ($matches as $tag)
                  _recordTag($stack, $tag[1], $tag[2]);
          }
          $out[] = $tok;
          if (!preg_match('/^(<[^>]+>)+$/', $tok))
              ++$words;
          if ($words == $num)
              break;
          $tok = strtok("\n\t ");
      }
      $abstract = _abstractRestore(implode(' ', $out));
      foreach ($stack as $tag)
      {
          $abstract .= "</$tag>";
      }
      return $abstract;
  }
  function _abstractProtect($match)
  {
      return preg_replace('/\s/', "\x01", $match[0]);
  }
  function _abstractRestore($strings)
  {
      return preg_replace('/\x01/', ' ', $strings);
  }
  function _recordTag(&$stack, $tag, $args)
  {
      // XHTML 
      if (strlen($args) and $args[strlen($args) - 1] == '/')
      {
          return;
      }
      elseif ($tag[0] == '/')
      {
          $tag = substr($tag, 1);
          for ($i = count($stack) - 1; $i >= 0; $i--)
          {
              if ($stack[$i] == $tag)
              {
                  array_splice($stack, $i, 1);
                  return;
              }
          }
          return;
      }
      elseif (in_array($tag, array('p', 'li', 'ul', 'ol', 'div', 'span', 'a')))
      {
          $stack[] = $tag;
      }
      else
      {
          // no-op 
      }
  }
  
  function imageFXResize($width, $height, $target)
  {
      if ($width > $height)
      {
          $percentage = ($target / $width);
      }
      else
      {
          $percentage = ($target / $height);
      }
      
      $width = round($width * $percentage);
      $height = round($height * $percentage);
      
      return "width=\"$width\" height=\"$height\"";
  }
?>