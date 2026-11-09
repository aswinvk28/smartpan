<?php

/* core/themes/seven/templates/page.html.twig */
class __TwigTemplate_7dcc0e3540dd6b98704f5303d18356b0ff05e9b214ca72de7a516a63f8a7e979 extends Drupal\Core\Template\TwigTemplate
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
        // line 68
        echo "  <header id=\"branding\" class=\"clearfix\">
    <div class=\"branding__inner\">
      ";
        // line 70
        echo twig_render_var($this->getContextReference($context, "title_prefix"));
        echo "
      ";
        // line 71
        if ((isset($context["title"]) ? $context["title"] : null)) {
            // line 72
            echo "        <h1 class=\"page-title\">";
            echo twig_render_var($this->getContextReference($context, "title"));
            echo "</h1>
      ";
        }
        // line 74
        echo "      ";
        echo twig_render_var($this->getContextReference($context, "title_suffix"));
        echo "
      ";
        // line 75
        if ((isset($context["primary_local_tasks"]) ? $context["primary_local_tasks"] : null)) {
            // line 76
            echo "        ";
            echo twig_render_var($this->getContextReference($context, "primary_local_tasks"));
            echo "
      ";
        }
        // line 78
        echo "    </div>
  </header>

  <div id=\"page\">
    ";
        // line 82
        if ((isset($context["secondary_local_tasks"]) ? $context["secondary_local_tasks"] : null)) {
            // line 83
            echo "      <div class=\"tabs-secondary clearfix\" role=\"navigation\">";
            echo twig_render_var($this->getContextReference($context, "secondary_local_tasks"));
            echo "</div>
    ";
        }
        // line 85
        echo "
    ";
        // line 86
        echo twig_render_var($this->getContextReference($context, "breadcrumb"));
        echo "

    <main id=\"content\" class=\"clearfix\" role=\"main\">
      <div class=\"visually-hidden\"><a id=\"main-content\"></a></div>
      ";
        // line 90
        if ((isset($context["messages"]) ? $context["messages"] : null)) {
            // line 91
            echo "        <div id=\"console\" class=\"clearfix\">";
            echo twig_render_var($this->getContextReference($context, "messages"));
            echo "</div>
      ";
        }
        // line 93
        echo "      ";
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "help")) {
            // line 94
            echo "        <div id=\"help\">
          ";
            // line 95
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "help"));
            echo "
        </div>
      ";
        }
        // line 98
        echo "      ";
        if ((isset($context["action_links"]) ? $context["action_links"] : null)) {
            // line 99
            echo "        <ul class=\"action-links\">
          ";
            // line 100
            echo twig_render_var($this->getContextReference($context, "action_links"));
            echo "
        </ul>
      ";
        }
        // line 103
        echo "      ";
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "content"));
        echo "
    </main>

    <footer id=\"footer\" role=\"contentinfo\">
      ";
        // line 107
        echo twig_render_var($this->getContextReference($context, "feed_icons"));
        echo "
    </footer>

  </div>
";
    }

    public function getTemplateName()
    {
        return "core/themes/seven/templates/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  112 => 107,  104 => 103,  98 => 100,  95 => 99,  92 => 98,  86 => 95,  83 => 94,  80 => 93,  74 => 91,  72 => 90,  62 => 85,  56 => 83,  42 => 76,  40 => 75,  29 => 72,  27 => 71,  76 => 58,  70 => 56,  67 => 54,  60 => 52,  55 => 49,  21 => 24,  85 => 44,  79 => 43,  65 => 86,  50 => 32,  47 => 47,  28 => 27,  77 => 58,  71 => 39,  66 => 54,  63 => 36,  57 => 50,  54 => 82,  35 => 74,  26 => 25,  24 => 26,  51 => 48,  48 => 78,  46 => 47,  43 => 46,  41 => 46,  36 => 29,  34 => 23,  32 => 28,  25 => 41,  23 => 70,  19 => 68,);
    }
}
