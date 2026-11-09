<?php

/* core/modules/system/templates/status-messages.html.twig */
class __TwigTemplate_5831a8ccf5a485a52f41b7bed3d694708e7ecb012b17cc73d1ccbf633bca7b79 extends Drupal\Core\Template\TwigTemplate
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 26
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["message_list"]) ? $context["message_list"] : null));
        foreach ($context['_seq'] as $context["type"] => $context["messages"]) {
            // line 27
            echo "  <div class=\"messages messages--";
            echo twig_render_var($this->getContextReference($context, "type"));
            echo "\" role=\"contentinfo\" aria-label=\"";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "status_headings"), $this->getContextReference($context, "type"), array(), "array"));
            echo "\">
    ";
            // line 28
            if (((isset($context["type"]) ? $context["type"] : null) == "error")) {
                // line 29
                echo "      <div role=\"alert\">
    ";
            }
            // line 31
            echo "      ";
            if ($this->getAttribute((isset($context["status_headings"]) ? $context["status_headings"] : null), (isset($context["type"]) ? $context["type"] : null), array(), "array")) {
                // line 32
                echo "        <h2 class=\"visually-hidden\">";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "status_headings"), $this->getContextReference($context, "type"), array(), "array"));
                echo "</h2>
      ";
            }
            // line 34
            echo "      ";
            if ((twig_length_filter($this->env, (isset($context["messages"]) ? $context["messages"] : null)) > 1)) {
                // line 35
                echo "        <ul class=\"messages__list\">
          ";
                // line 36
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["messages"]) ? $context["messages"] : null));
                foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                    // line 37
                    echo "            <li class=\"messages__item\">";
                    echo twig_render_var($this->getContextReference($context, "message"));
                    echo "</li>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 39
                echo "        </ul>
      ";
            } else {
                // line 41
                echo "        ";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "messages"), 0));
                echo "
      ";
            }
            // line 43
            echo "    ";
            if (((isset($context["type"]) ? $context["type"] : null) == "error")) {
                // line 44
                echo "      </div>
    ";
            }
            // line 46
            echo "  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['type'], $context['messages'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/status-messages.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 44,  74 => 43,  68 => 41,  48 => 35,  45 => 34,  39 => 32,  60 => 52,  57 => 50,  51 => 36,  47 => 47,  35 => 43,  25 => 41,  354 => 209,  348 => 206,  345 => 205,  343 => 204,  340 => 203,  334 => 200,  330 => 199,  326 => 198,  322 => 197,  319 => 196,  317 => 195,  312 => 192,  306 => 189,  302 => 188,  298 => 187,  295 => 186,  293 => 185,  288 => 182,  282 => 179,  279 => 178,  277 => 177,  274 => 176,  268 => 173,  265 => 172,  263 => 171,  257 => 168,  252 => 167,  246 => 164,  243 => 163,  241 => 162,  236 => 161,  230 => 158,  227 => 157,  225 => 156,  220 => 155,  214 => 152,  211 => 151,  209 => 150,  205 => 149,  202 => 148,  196 => 147,  190 => 144,  186 => 142,  180 => 139,  177 => 138,  175 => 137,  172 => 136,  166 => 133,  163 => 132,  161 => 131,  157 => 129,  151 => 126,  148 => 125,  141 => 122,  138 => 121,  128 => 116,  121 => 115,  119 => 114,  103 => 109,  96 => 108,  83 => 103,  75 => 101,  72 => 100,  63 => 98,  61 => 97,  50 => 93,  38 => 90,  32 => 29,  29 => 86,  27 => 85,  23 => 27,  26 => 25,  21 => 39,  155 => 93,  149 => 90,  146 => 124,  143 => 88,  137 => 85,  134 => 119,  131 => 83,  125 => 81,  122 => 80,  116 => 113,  113 => 112,  110 => 75,  104 => 73,  102 => 72,  99 => 71,  93 => 107,  90 => 67,  84 => 64,  81 => 46,  79 => 62,  76 => 58,  70 => 56,  67 => 54,  64 => 39,  58 => 96,  55 => 37,  52 => 51,  46 => 48,  43 => 46,  41 => 91,  36 => 31,  30 => 28,  28 => 42,  24 => 41,  19 => 26,);
    }
}
