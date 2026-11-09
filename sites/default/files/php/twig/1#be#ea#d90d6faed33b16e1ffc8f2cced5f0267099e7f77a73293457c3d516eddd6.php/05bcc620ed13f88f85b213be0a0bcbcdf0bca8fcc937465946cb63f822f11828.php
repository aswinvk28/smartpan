<?php

/* core/modules/system/templates/item-list.html.twig */
class __TwigTemplate_beead90d6faed33b16e1ffc8f2cced5f0267099e7f77a73293457c3d516eddd6 extends Drupal\Core\Template\TwigTemplate
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
        // line 21
        if (((isset($context["items"]) ? $context["items"] : null) || (isset($context["empty"]) ? $context["empty"] : null))) {
            // line 22
            echo "<div class=\"item-list\">";
            // line 23
            if ((isset($context["title"]) ? $context["title"] : null)) {
                // line 24
                echo "<h3>";
                echo twig_render_var($this->getContextReference($context, "title"));
                echo "</h3>";
            }
            // line 26
            if ((isset($context["items"]) ? $context["items"] : null)) {
                // line 27
                echo "<";
                echo twig_render_var($this->getContextReference($context, "list_type"));
                echo twig_render_var($this->getContextReference($context, "attributes"));
                echo ">";
                // line 28
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["items"]) ? $context["items"] : null));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 29
                    echo "<li";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "attributes"));
                    echo ">";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "value"));
                    echo "</li>";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 31
                echo "</";
                echo twig_render_var($this->getContextReference($context, "list_type"));
                echo ">";
            } else {
                // line 33
                echo twig_render_var($this->getContextReference($context, "empty"));
            }
            // line 35
            echo "</div>";
        }
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/item-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 35,  56 => 33,  37 => 28,  71 => 55,  66 => 54,  54 => 50,  44 => 51,  42 => 19,  33 => 17,  77 => 58,  74 => 43,  68 => 41,  48 => 48,  45 => 34,  39 => 32,  60 => 52,  57 => 51,  51 => 31,  47 => 47,  35 => 44,  25 => 24,  354 => 209,  348 => 206,  345 => 205,  343 => 204,  340 => 203,  334 => 200,  330 => 199,  326 => 198,  322 => 197,  319 => 196,  317 => 195,  312 => 192,  306 => 189,  302 => 188,  298 => 187,  295 => 186,  293 => 185,  288 => 182,  282 => 179,  279 => 178,  277 => 177,  274 => 176,  268 => 173,  265 => 172,  263 => 171,  257 => 168,  252 => 167,  246 => 164,  243 => 163,  241 => 162,  236 => 161,  230 => 158,  227 => 157,  225 => 156,  220 => 155,  214 => 152,  211 => 151,  209 => 150,  205 => 149,  202 => 148,  196 => 147,  190 => 144,  186 => 142,  180 => 139,  177 => 138,  175 => 137,  172 => 136,  166 => 133,  163 => 132,  161 => 131,  157 => 129,  151 => 126,  148 => 125,  141 => 122,  138 => 121,  128 => 116,  121 => 115,  119 => 114,  103 => 109,  96 => 108,  83 => 103,  75 => 101,  72 => 100,  63 => 53,  61 => 97,  50 => 93,  38 => 49,  32 => 27,  29 => 16,  27 => 85,  23 => 23,  26 => 41,  21 => 22,  155 => 93,  149 => 90,  146 => 124,  143 => 88,  137 => 85,  134 => 119,  131 => 83,  125 => 81,  122 => 80,  116 => 113,  113 => 112,  110 => 75,  104 => 73,  102 => 72,  99 => 71,  93 => 107,  90 => 67,  84 => 64,  81 => 46,  79 => 62,  76 => 58,  70 => 56,  67 => 54,  64 => 39,  58 => 96,  55 => 37,  52 => 51,  46 => 47,  43 => 46,  41 => 29,  36 => 31,  30 => 26,  28 => 46,  24 => 40,  19 => 21,);
    }
}
