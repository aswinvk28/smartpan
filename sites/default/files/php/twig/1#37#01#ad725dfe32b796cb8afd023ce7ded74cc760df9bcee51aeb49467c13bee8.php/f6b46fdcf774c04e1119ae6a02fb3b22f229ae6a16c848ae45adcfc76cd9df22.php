<?php

/* themes/flatui/templates/menu/main-menu.html.twig */
class __TwigTemplate_3701ad725dfe32b796cb8afd023ce7ded74cc760df9bcee51aeb49467c13bee8 extends Drupal\Core\Template\TwigTemplate
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
        // line 1
        if ((isset($context["rest_links"]) ? $context["rest_links"] : null)) {
            // line 2
            echo "<div class=\"clearfix decolumn_mini\">
        <ul class=\"nav nav-pills pull-left\">
            ";
            // line 4
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["rest_links"]) ? $context["rest_links"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["rest_link"]) {
                // line 5
                echo "            <li class=\"\"><a class=\"nounderline\" href=\"";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "rest_link"), "href"));
                echo "\">";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "rest_link"), "title"));
                echo "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rest_link'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 7
            echo "        </ul>
        <ul class=\"nav nav-pills pull-right\">";
            // line 9
            if ((isset($context["secondary_menu"]) ? $context["secondary_menu"] : null)) {
                // line 10
                if ((isset($context["account_links"]) ? $context["account_links"] : null)) {
                    // line 11
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable((isset($context["account_links"]) ? $context["account_links"] : null));
                    foreach ($context['_seq'] as $context["_key"] => $context["account_link"]) {
                        // line 12
                        echo "                    <li class=\"\"><a class=\"nounderline\" href=\"";
                        echo twig_render_var($this->getAttribute($this->getContextReference($context, "account_link"), "href"));
                        echo "\">";
                        echo twig_render_var($this->getAttribute($this->getContextReference($context, "account_link"), "title"));
                        echo "</a></li>
                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account_link'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                }
                // line 15
                echo "<li>";
                echo twig_render_var($this->getContextReference($context, "secondary_menu"));
                echo "</li>";
            } elseif ((isset($context["non_account_links"]) ? $context["non_account_links"] : null)) {
                // line 17
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["non_account_links"]) ? $context["non_account_links"] : null));
                foreach ($context['_seq'] as $context["_key"] => $context["non_account_link"]) {
                    // line 18
                    echo "                <li><a class=\"btn btn-success\" href=\"";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "non_account_link"), "href"));
                    echo "\">";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "non_account_link"), "title"));
                    echo "</a></li>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['non_account_link'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
            // line 21
            echo "</ul>
    </div>";
        }
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/menu/main-menu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 18,  68 => 17,  63 => 15,  51 => 12,  47 => 11,  40 => 7,  25 => 4,  289 => 110,  282 => 106,  274 => 104,  272 => 103,  267 => 100,  261 => 97,  258 => 96,  255 => 95,  249 => 92,  246 => 91,  244 => 90,  239 => 88,  234 => 87,  228 => 84,  225 => 83,  223 => 82,  218 => 81,  212 => 78,  209 => 77,  206 => 76,  200 => 73,  196 => 72,  192 => 71,  187 => 70,  185 => 69,  178 => 65,  175 => 64,  164 => 62,  158 => 59,  153 => 57,  145 => 54,  139 => 51,  136 => 50,  133 => 49,  129 => 47,  123 => 44,  108 => 39,  97 => 34,  89 => 29,  73 => 22,  71 => 21,  66 => 18,  61 => 16,  54 => 13,  45 => 10,  39 => 8,  34 => 6,  31 => 5,  29 => 5,  22 => 2,  26 => 3,  21 => 2,  155 => 58,  149 => 56,  146 => 89,  143 => 88,  137 => 85,  134 => 84,  131 => 83,  125 => 81,  122 => 80,  116 => 43,  113 => 42,  110 => 75,  104 => 73,  102 => 36,  99 => 35,  93 => 68,  90 => 67,  84 => 21,  81 => 63,  79 => 62,  76 => 23,  70 => 58,  67 => 57,  64 => 17,  58 => 15,  55 => 52,  52 => 51,  46 => 48,  43 => 9,  41 => 46,  36 => 7,  30 => 43,  28 => 42,  24 => 41,  19 => 1,);
    }
}
