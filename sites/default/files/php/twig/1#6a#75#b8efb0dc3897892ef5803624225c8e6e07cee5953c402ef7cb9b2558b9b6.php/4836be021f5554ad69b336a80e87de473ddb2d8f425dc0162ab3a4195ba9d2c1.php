<?php

/* core/modules/block/templates/block.html.twig */
class __TwigTemplate_6a75b8efb0dc3897892ef5803624225c8e6e07cee5953c402ef7cb9b2558b9b6 extends Drupal\Core\Template\TwigTemplate
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
        return array (  44 => 51,  38 => 49,  81 => 46,  68 => 41,  64 => 39,  45 => 34,  39 => 32,  30 => 47,  33 => 17,  112 => 107,  104 => 103,  98 => 100,  95 => 99,  92 => 98,  86 => 95,  83 => 94,  80 => 93,  74 => 43,  72 => 90,  62 => 85,  56 => 83,  42 => 19,  40 => 75,  29 => 16,  27 => 71,  76 => 58,  70 => 56,  67 => 54,  60 => 52,  55 => 37,  21 => 13,  85 => 44,  79 => 43,  65 => 86,  50 => 32,  47 => 47,  28 => 46,  77 => 44,  71 => 39,  66 => 54,  63 => 36,  57 => 50,  54 => 82,  35 => 74,  26 => 25,  24 => 45,  51 => 36,  48 => 52,  46 => 47,  43 => 46,  41 => 46,  36 => 31,  34 => 23,  32 => 29,  25 => 41,  23 => 27,  19 => 44,);
    }
}
