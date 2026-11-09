<?php

/* core/modules/toolbar/templates/toolbar.html.twig */
class __TwigTemplate_c2f14f1623575c4b428a935b8800fb2fb51e3bbecfa6594e0a8166a2956dc819 extends Drupal\Core\Template\TwigTemplate
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
        // line 25
        echo "<nav";
        echo twig_render_var($this->getContextReference($context, "attributes"));
        echo ">
  <div";
        // line 26
        echo twig_render_var($this->getContextReference($context, "toolbar_attributes"));
        echo ">
    <h2 class=\"visually-hidden\">";
        // line 27
        echo twig_render_var($this->getContextReference($context, "toolbar_heading"));
        echo "</h2>
    ";
        // line 28
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["tabs"]) ? $context["tabs"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["tab"]) {
            // line 29
            echo "      <div";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "tab"), "attributes"));
            echo ">";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "tab"), "link"));
            echo "</div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tab'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "  </div>
  ";
        // line 32
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["trays"]) ? $context["trays"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["tray"]) {
            // line 33
            echo "    ";
            ob_start();
            // line 34
            echo "    <div";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "tray"), "attributes"));
            echo ">
      <div class=\"toolbar-lining clearfix\">
        ";
            // line 36
            if ($this->getAttribute((isset($context["tray"]) ? $context["tray"] : null), "label")) {
                // line 37
                echo "          <h3 class=\"toolbar-tray-name visually-hidden\">";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "tray"), "label"));
                echo "</h3>
        ";
            }
            // line 39
            echo "        ";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "tray"), "links"));
            echo "
      </div>
    </div>
    ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
            // line 43
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tray'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 44
        echo "  ";
        echo twig_render_var($this->getContextReference($context, "remainder"));
        echo "
</nav>
";
    }

    public function getTemplateName()
    {
        return "core/modules/toolbar/templates/toolbar.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 44,  79 => 43,  65 => 37,  50 => 32,  47 => 31,  28 => 27,  77 => 58,  71 => 39,  66 => 54,  63 => 36,  57 => 34,  54 => 33,  35 => 44,  26 => 41,  24 => 26,  51 => 32,  48 => 48,  46 => 47,  43 => 27,  41 => 46,  36 => 29,  34 => 23,  32 => 28,  25 => 20,  23 => 19,  19 => 25,);
    }
}
