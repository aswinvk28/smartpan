<?php

/* themes/flatui/templates/page.html.twig */
class __TwigTemplate_cef007873a455dcb8770f867fcc7ee686fcabf7144e518d5ed3d5cefd1a6d865 extends Drupal\Core\Template\TwigTemplate
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
        echo "<div id=\"page-wrapper\"><div id=\"page\" class=\"page\">
    <header class=\"header clearfix ";
        // line 2
        if ((isset($context["is_admin"]) ? $context["is_admin"] : null)) {
            echo " header-admin ";
        }
        echo "\">
        <section class=\"container\">
            ";
        // line 4
        if (((isset($context["logo"]) ? $context["logo"] : null) || (isset($context["main_menu"]) ? $context["main_menu"] : null))) {
            // line 5
            echo "                <div class=\"header-container clearfix\">
                    ";
            // line 6
            if ((isset($context["logo"]) ? $context["logo"] : null)) {
                // line 7
                echo "                        <div class=\"logo-container span5\">
                            <a href=\"";
                // line 8
                echo twig_render_var($this->getContextReference($context, "front_page"));
                echo "\" title=\"";
                echo twig_render_var(t("Home"));
                echo "\" rel=\"home\" id=\"logo\">
                                <img src=\"";
                // line 9
                echo twig_render_var($this->getContextReference($context, "logo"));
                echo "\" alt=\"";
                echo twig_render_var(t("Home"));
                echo "\" />
                            </a>
                        </div>
                    ";
            }
            // line 13
            echo "                </div>
            ";
        }
        // line 15
        echo "            ";
        if ((((isset($context["site_name"]) ? $context["site_name"] : null) || (isset($context["site_slogan"]) ? $context["site_slogan"] : null)) || (isset($context["main_menu"]) ? $context["main_menu"] : null))) {
            // line 16
            echo "                <div class=\"header-container clearfix\">
                    ";
            // line 17
            if (((isset($context["site_name"]) ? $context["site_name"] : null) || (isset($context["main_menu"]) ? $context["main_menu"] : null))) {
                // line 18
                echo "                        <div class=\"navbar\">
                            <div class=\"navbar-inner clearfix\">
                                <div class=\"row-fluid column_mini\">
                                    ";
                // line 21
                if ((isset($context["site_name"]) ? $context["site_name"] : null)) {
                    // line 22
                    echo "                                    <ul class=\"unstyled span2 vertical-fluid main_menu\" role=\"navigation\">
                                        <li class=\"\"><a class=\"brand nounderline\" href=\"";
                    // line 23
                    echo twig_render_var($this->getContextReference($context, "front_page"));
                    echo "\">";
                    echo twig_render_var($this->getContextReference($context, "site_name"));
                    echo "</a></li>
                                    </ul>
                                    ";
                }
                // line 26
                echo "                                    <div class=\"form-search-container span6 vertical-fluid\">
                                        <div class=\"row-fluid\">
                                            <div class=\"span9\">
                                                ";
                // line 29
                echo twig_render_var($this->getContextReference($context, "stock_symbol_form"));
                echo "
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ";
                // line 34
                if ((isset($context["main_menu"]) ? $context["main_menu"] : null)) {
                    // line 35
                    echo "                                <div class=\"clearfix\">
                                    ";
                    // line 36
                    echo twig_render_var($this->getContextReference($context, "main_menu"));
                    echo "
                                </div>
                                ";
                }
                // line 39
                echo "                            </div>
                        </div>
                    ";
            }
            // line 42
            echo "                    ";
            if ((isset($context["site_slogan"]) ? $context["site_slogan"] : null)) {
                // line 43
                echo "                        <div ";
                if ((isset($context["hide_site_slogan"]) ? $context["hide_site_slogan"] : null)) {
                    echo " class=\"hide\"";
                }
                echo ">
                            ";
                // line 44
                echo twig_render_var($this->getContextReference($context, "site_slogan"));
                echo "
                        </div>
                    ";
            }
            // line 47
            echo "                </div>
            ";
        }
        // line 49
        echo "            ";
        if ((isset($context["tabs"]) ? $context["tabs"] : null)) {
            // line 50
            echo "                <div class=\"tabs clearfix\" role=\"navigation\">
                    ";
            // line 51
            echo twig_render_var($this->getContextReference($context, "tabs"));
            echo "
                </div>
            ";
        }
        // line 54
        echo "        </section>
    </header>
    ";
        // line 56
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "header"));
        echo "
    ";
        // line 57
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "featured")) {
            // line 58
            echo "        <aside id=\"featured\"><div class=\"section clearfix\">
            ";
            // line 59
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "featured"));
            echo "
        </div></aside>
    ";
        }
        // line 62
        echo "    <div class=\"app form-description background-sep-hash clearfix ";
        if ((isset($context["is_admin"]) ? $context["is_admin"] : null)) {
            echo " app-admin ";
        }
        echo " ";
        if ((isset($context["is_dashboard"]) ? $context["is_dashboard"] : null)) {
            echo " dark ";
        }
        echo "\">
        ";
        // line 64
        echo "        <div id=\"messages\" class=\"section\"><div class=\"container status-messages\">
            ";
        // line 65
        echo twig_render_var($this->getContextReference($context, "messages"));
        echo "
        </div></div>
        <section id=\"page-container\" class=\"container\">
            <div class=\"clearfix\">
                ";
        // line 69
        if ((isset($context["title"]) ? $context["title"] : null)) {
            // line 70
            echo "                    <div class=\"page-title\" ";
            echo twig_render_var($this->getContextReference($context, "title_attributes"));
            echo ">
                    ";
            // line 71
            echo twig_render_var($this->getContextReference($context, "title_prefix"));
            echo "
                        <h3>";
            // line 72
            echo twig_render_var($this->getContextReference($context, "title"));
            echo "</h3>
                    ";
            // line 73
            echo twig_render_var($this->getContextReference($context, "title_suffix"));
            echo "
                    </div>
                ";
        }
        // line 76
        echo "                ";
        if ((isset($context["page_description"]) ? $context["page_description"] : null)) {
            // line 77
            echo "                    <div class=\"page-description\">
                        <p class=\"fsmall gray\">";
            // line 78
            echo twig_render_var($this->getContextReference($context, "page_description"));
            echo "</p>
                    </div>
                ";
        }
        // line 81
        echo "                ";
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "help"));
        echo "
                ";
        // line 82
        if ((isset($context["action_links"]) ? $context["action_links"] : null)) {
            // line 83
            echo "                    <ul class=\"action-links\">
                        ";
            // line 84
            echo twig_render_var($this->getContextReference($context, "action_links"));
            echo "
                    </ul>
                ";
        }
        // line 87
        echo "                ";
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "content"));
        echo "
                ";
        // line 88
        echo twig_render_var($this->getContextReference($context, "feed_icons"));
        echo "
            </div>
            ";
        // line 90
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "sidebar")) {
            // line 91
            echo "                <div class=\"column span5\"><aside class=\"section\">
                    ";
            // line 92
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "sidebar"));
            echo "
                </aside></div>
            ";
        }
        // line 95
        echo "            ";
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "sidebar_mini")) {
            // line 96
            echo "              <div class=\"column span3\"><aside class=\"section\">
                ";
            // line 97
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "sidebar_mini"));
            echo "
              </aside></div>
            ";
        }
        // line 100
        echo "            <div id=\"page-gridster\" class=\"gridster row\"></div>
        </section>
    </div>
    ";
        // line 103
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer")) {
            // line 104
            echo "        <footer id=\"footer\" role=\"contentinfo\" class=\"clearfix footer ";
            if ((isset($context["is_dashboard"]) ? $context["is_dashboard"] : null)) {
                echo " background-sep-hash dark ";
            }
            echo "\">
            <div class=\"container\">
                ";
            // line 106
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer"));
            echo "
            </div>
        </footer>
    ";
        }
        // line 110
        echo "</div></div>";
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  289 => 110,  282 => 106,  274 => 104,  272 => 103,  267 => 100,  261 => 97,  258 => 96,  255 => 95,  249 => 92,  246 => 91,  244 => 90,  239 => 88,  234 => 87,  228 => 84,  225 => 83,  223 => 82,  218 => 81,  212 => 78,  209 => 77,  206 => 76,  200 => 73,  196 => 72,  192 => 71,  187 => 70,  185 => 69,  178 => 65,  175 => 64,  164 => 62,  158 => 59,  153 => 57,  145 => 54,  139 => 51,  136 => 50,  133 => 49,  129 => 47,  123 => 44,  108 => 39,  97 => 34,  89 => 29,  73 => 22,  71 => 21,  66 => 18,  61 => 16,  54 => 13,  45 => 9,  39 => 8,  34 => 6,  31 => 5,  29 => 4,  22 => 2,  26 => 16,  21 => 15,  155 => 58,  149 => 56,  146 => 89,  143 => 88,  137 => 85,  134 => 84,  131 => 83,  125 => 81,  122 => 80,  116 => 43,  113 => 42,  110 => 75,  104 => 73,  102 => 36,  99 => 35,  93 => 68,  90 => 67,  84 => 26,  81 => 63,  79 => 62,  76 => 23,  70 => 58,  67 => 57,  64 => 17,  58 => 15,  55 => 52,  52 => 51,  46 => 48,  43 => 47,  41 => 46,  36 => 7,  30 => 43,  28 => 42,  24 => 41,  19 => 1,);
    }
}
