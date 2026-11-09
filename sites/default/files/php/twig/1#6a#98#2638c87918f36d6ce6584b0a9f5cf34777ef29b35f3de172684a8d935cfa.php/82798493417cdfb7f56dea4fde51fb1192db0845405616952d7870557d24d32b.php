<?php

/* core/modules/block/templates/block.html.twig */
class __TwigTemplate_6a982638c87918f36d6ce6584b0a9f5cf34777ef29b35f3de172684a8d935cfa extends Drupal\Core\Template\TwigTemplate
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
        // line 44
        echo "<div";
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo ">
  ";
        // line 45
        echo twig_render_var($this->getContextReference($context, "title_prefix"));
        echo "
  ";
        // line 46
        if ((isset($context["label"]) ? $context["label"] : null)) {
            // line 47
            echo "    <h2";
            echo twig_render_var($this->getContextReference($context, "title_attributes"));
            echo ">";
            echo twig_render_var($this->getContextReference($context, "label"));
            echo "</h2>
  ";
        }
        // line 49
        echo "  ";
        echo twig_render_var($this->getContextReference($context, "title_suffix"));
        echo "

  <div";
        // line 51
        echo twig_render_var($this->getContextReference($context, "content_attributes"));
        echo ">
    ";
        // line 52
        echo twig_render_var($this->getContextReference($context, "content"));
        echo "
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "core/modules/block/templates/block.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 51,  42 => 19,  33 => 17,  77 => 44,  74 => 43,  68 => 41,  48 => 52,  45 => 34,  39 => 32,  60 => 52,  57 => 50,  51 => 36,  47 => 47,  35 => 43,  25 => 41,  354 => 209,  348 => 206,  345 => 205,  343 => 204,  340 => 203,  334 => 200,  330 => 199,  326 => 198,  322 => 197,  319 => 196,  317 => 195,  312 => 192,  306 => 189,  302 => 188,  298 => 187,  295 => 186,  293 => 185,  288 => 182,  282 => 179,  279 => 178,  277 => 177,  274 => 176,  268 => 173,  265 => 172,  263 => 171,  257 => 168,  252 => 167,  246 => 164,  243 => 163,  241 => 162,  236 => 161,  230 => 158,  227 => 157,  225 => 156,  220 => 155,  214 => 152,  211 => 151,  209 => 150,  205 => 149,  202 => 148,  196 => 147,  190 => 144,  186 => 142,  180 => 139,  177 => 138,  175 => 137,  172 => 136,  166 => 133,  163 => 132,  161 => 131,  157 => 129,  151 => 126,  148 => 125,  141 => 122,  138 => 121,  128 => 116,  121 => 115,  119 => 114,  103 => 109,  96 => 108,  83 => 103,  75 => 101,  72 => 100,  63 => 98,  61 => 97,  50 => 93,  38 => 49,  32 => 29,  29 => 16,  27 => 85,  23 => 27,  26 => 25,  21 => 13,  155 => 93,  149 => 90,  146 => 124,  143 => 88,  137 => 85,  134 => 119,  131 => 83,  125 => 81,  122 => 80,  116 => 113,  113 => 112,  110 => 75,  104 => 73,  102 => 72,  99 => 71,  93 => 107,  90 => 67,  84 => 64,  81 => 46,  79 => 62,  76 => 58,  70 => 56,  67 => 54,  64 => 39,  58 => 96,  55 => 37,  52 => 51,  46 => 48,  43 => 46,  41 => 91,  36 => 31,  30 => 47,  28 => 46,  24 => 45,  19 => 44,);
    }
}
