<?php

/* core/themes/bartik/templates/page.html.twig */
class __TwigTemplate_a1cae3b0e8a87a76f43546d06efd7dd013beaabfd3ea3d848813e5fa317015de extends Drupal\Core\Template\TwigTemplate
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
        // line 82
        echo "<div id=\"page-wrapper\"><div id=\"page\">

  <header id=\"header\" class=\"";
        // line 84
        echo twig_render_var((($this->getContextReference($context, "secondary_menu")) ? ("with-secondary-menu") : ("without-secondary-menu")));
        echo "\" role=\"banner\"><div class=\"section clearfix\">
   ";
        // line 85
        if ((isset($context["secondary_menu"]) ? $context["secondary_menu"] : null)) {
            // line 86
            echo "      <nav id=\"secondary-menu\" class=\"navigation\" role=\"navigation\">
        ";
            // line 87
            echo twig_render_var($this->getContextReference($context, "secondary_menu"));
            echo "
      </nav> <!-- /#secondary-menu -->
    ";
        }
        // line 90
        echo "
    ";
        // line 91
        if ((isset($context["logo"]) ? $context["logo"] : null)) {
            // line 92
            echo "      <a href=\"";
            echo twig_render_var($this->getContextReference($context, "front_page"));
            echo "\" title=\"";
            echo twig_render_var(t("Home"));
            echo "\" rel=\"home\" id=\"logo\">
        <img src=\"";
            // line 93
            echo twig_render_var($this->getContextReference($context, "logo"));
            echo "\" alt=\"";
            echo twig_render_var(t("Home"));
            echo "\" />
      </a>
    ";
        }
        // line 96
        echo "
    ";
        // line 97
        if (((isset($context["site_name"]) ? $context["site_name"] : null) || (isset($context["site_slogan"]) ? $context["site_slogan"] : null))) {
            // line 98
            echo "      <div id=\"name-and-slogan\"";
            if (((isset($context["hide_site_name"]) ? $context["hide_site_name"] : null) && (isset($context["hide_site_slogan"]) ? $context["hide_site_slogan"] : null))) {
                echo " class=\"visually-hidden\"";
            }
            echo ">
        ";
            // line 99
            if ((isset($context["site_name"]) ? $context["site_name"] : null)) {
                // line 100
                echo "          ";
                if ((isset($context["title"]) ? $context["title"] : null)) {
                    // line 101
                    echo "            <div id=\"site-name\"";
                    if ((isset($context["hide_site_name"]) ? $context["hide_site_name"] : null)) {
                        echo " class=\"visually-hidden\"";
                    }
                    echo ">
              <strong>
                <a href=\"";
                    // line 103
                    echo twig_render_var($this->getContextReference($context, "front_page"));
                    echo "\" title=\"";
                    echo twig_render_var(t("Home"));
                    echo "\" rel=\"home\"><span>";
                    echo twig_render_var($this->getContextReference($context, "site_name"));
                    echo "</span></a>
              </strong>
            </div>
          ";
                    // line 107
                    echo "          ";
                } else {
                    // line 108
                    echo "            <h1 id=\"site-name\"";
                    if ((isset($context["hide_site_name"]) ? $context["hide_site_name"] : null)) {
                        echo " class=\"visually-hidden\" ";
                    }
                    echo ">
              <a href=\"";
                    // line 109
                    echo twig_render_var($this->getContextReference($context, "front_page"));
                    echo "\" title=\"";
                    echo twig_render_var(t("Home"));
                    echo "\" rel=\"home\"><span>";
                    echo twig_render_var($this->getContextReference($context, "site_name"));
                    echo "</span></a>
            </h1>
          ";
                }
                // line 112
                echo "        ";
            }
            // line 113
            echo "
        ";
            // line 114
            if ((isset($context["site_slogan"]) ? $context["site_slogan"] : null)) {
                // line 115
                echo "          <div id=\"site-slogan\"";
                if ((isset($context["hide_site_slogan"]) ? $context["hide_site_slogan"] : null)) {
                    echo " class=\"visually-hidden\"";
                }
                echo ">
            ";
                // line 116
                echo twig_render_var($this->getContextReference($context, "site_slogan"));
                echo "
          </div>
        ";
            }
            // line 119
            echo "      </div><!-- /#name-and-slogan -->
    ";
        }
        // line 121
        echo "
    ";
        // line 122
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "header"));
        echo "

    ";
        // line 124
        if ((isset($context["main_menu"]) ? $context["main_menu"] : null)) {
            // line 125
            echo "      <nav id =\"main-menu\" class=\"navigation\" role=\"navigation\">
        ";
            // line 126
            echo twig_render_var($this->getContextReference($context, "main_menu"));
            echo "
      </nav> <!-- /#main-menu -->
    ";
        }
        // line 129
        echo "  </div></header> <!-- /.section, /#header-->

  ";
        // line 131
        if ((isset($context["messages"]) ? $context["messages"] : null)) {
            // line 132
            echo "    <div id=\"messages\"><div class=\"section clearfix\">
      ";
            // line 133
            echo twig_render_var($this->getContextReference($context, "messages"));
            echo "
    </div></div> <!-- /.section, /#messages -->
  ";
        }
        // line 136
        echo "
  ";
        // line 137
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "featured")) {
            // line 138
            echo "    <aside id=\"featured\"><div class=\"section clearfix\">
      ";
            // line 139
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "featured"));
            echo "
    </div></aside> <!-- /.section, /#featured -->
  ";
        }
        // line 142
        echo "
  <div id=\"main-wrapper\" class=\"clearfix\"><div id=\"main\" class=\"clearfix\">
    ";
        // line 144
        echo twig_render_var($this->getContextReference($context, "breadcrumb"));
        echo "

    <main id=\"content\" class=\"column\" role=\"main\"><section class=\"section\">
      ";
        // line 147
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "highlighted")) {
            echo "<div id=\"highlighted\">";
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "highlighted"));
            echo "</div>";
        }
        // line 148
        echo "      <a id=\"main-content\"></a>
      ";
        // line 149
        echo twig_render_var($this->getContextReference($context, "title_prefix"));
        echo "
        ";
        // line 150
        if ((isset($context["title"]) ? $context["title"] : null)) {
            // line 151
            echo "          <h1 class=\"title\" id=\"page-title\">
            ";
            // line 152
            echo twig_render_var($this->getContextReference($context, "title"));
            echo "
          </h1>
        ";
        }
        // line 155
        echo "      ";
        echo twig_render_var($this->getContextReference($context, "title_suffix"));
        echo "
        ";
        // line 156
        if ((isset($context["tabs"]) ? $context["tabs"] : null)) {
            // line 157
            echo "          <nav class=\"tabs\" role=\"navigation\">
            ";
            // line 158
            echo twig_render_var($this->getContextReference($context, "tabs"));
            echo "
          </nav>
        ";
        }
        // line 161
        echo "      ";
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "help"));
        echo "
        ";
        // line 162
        if ((isset($context["action_links"]) ? $context["action_links"] : null)) {
            // line 163
            echo "          <ul class=\"action-links\">
            ";
            // line 164
            echo twig_render_var($this->getContextReference($context, "action_links"));
            echo "
          </ul>
        ";
        }
        // line 167
        echo "      ";
        echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "content"));
        echo "
      ";
        // line 168
        echo twig_render_var($this->getContextReference($context, "feed_icons"));
        echo "
    </section></main> <!-- /.section, /#content -->

    ";
        // line 171
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "sidebar_first")) {
            // line 172
            echo "      <div id=\"sidebar-first\" class=\"column sidebar\"><aside class=\"section\">
        ";
            // line 173
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "sidebar_first"));
            echo "
      </aside></div><!-- /.section, /#sidebar-first -->
    ";
        }
        // line 176
        echo "
    ";
        // line 177
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "sidebar_second")) {
            // line 178
            echo "      <div id=\"sidebar-second\" class=\"column sidebar\"><aside class=\"section\">
        ";
            // line 179
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "sidebar_second"));
            echo "
      </aside></div><!-- /.section, /#sidebar-second -->
    ";
        }
        // line 182
        echo "
  </div></div><!-- /#main, /#main-wrapper -->

  ";
        // line 185
        if ((($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "triptych_first") || $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "triptych_middle")) || $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "triptych_last"))) {
            // line 186
            echo "    <div id=\"triptych-wrapper\"><aside id=\"triptych\" class=\"clearfix\">
      ";
            // line 187
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "triptych_first"));
            echo "
      ";
            // line 188
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "triptych_middle"));
            echo "
      ";
            // line 189
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "triptych_last"));
            echo "
    </aside></div><!-- /#triptych, /#triptych-wrapper -->
  ";
        }
        // line 192
        echo "
  <div id=\"footer-wrapper\"><footer class=\"section\">

    ";
        // line 195
        if (((($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer_firstcolumn") || $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer_secondcolumn")) || $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer_thirdcolumn")) || $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer_fourthcolumn"))) {
            // line 196
            echo "      <div id=\"footer-columns\" class=\"clearfix\">
        ";
            // line 197
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer_firstcolumn"));
            echo "
        ";
            // line 198
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer_secondcolumn"));
            echo "
        ";
            // line 199
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer_thirdcolumn"));
            echo "
        ";
            // line 200
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer_fourthcolumn"));
            echo "
      </div><!-- /#footer-columns -->
    ";
        }
        // line 203
        echo "
    ";
        // line 204
        if ($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "footer")) {
            // line 205
            echo "      <div id=\"footer\" role=\"contentinfo\" class=\"clearfix\">
        ";
            // line 206
            echo twig_render_var($this->getAttribute($this->getContextReference($context, "page"), "footer"));
            echo "
      </div> <!-- /#footer -->
   ";
        }
        // line 209
        echo "
  </footer></div> <!-- /.section, /#footer-wrapper -->

</div></div> <!-- /#page, /#page-wrapper -->
";
    }

    public function getTemplateName()
    {
        return "core/themes/bartik/templates/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  354 => 209,  348 => 206,  345 => 205,  343 => 204,  340 => 203,  334 => 200,  330 => 199,  326 => 198,  322 => 197,  319 => 196,  317 => 195,  312 => 192,  306 => 189,  302 => 188,  298 => 187,  295 => 186,  293 => 185,  288 => 182,  282 => 179,  279 => 178,  277 => 177,  274 => 176,  268 => 173,  265 => 172,  263 => 171,  257 => 168,  252 => 167,  246 => 164,  243 => 163,  241 => 162,  236 => 161,  230 => 158,  227 => 157,  225 => 156,  220 => 155,  214 => 152,  211 => 151,  209 => 150,  205 => 149,  202 => 148,  196 => 147,  190 => 144,  186 => 142,  180 => 139,  177 => 138,  175 => 137,  172 => 136,  166 => 133,  163 => 132,  161 => 131,  157 => 129,  151 => 126,  148 => 125,  141 => 122,  138 => 121,  128 => 116,  121 => 115,  119 => 114,  103 => 109,  96 => 108,  83 => 103,  75 => 101,  72 => 100,  63 => 98,  61 => 97,  50 => 93,  38 => 90,  32 => 87,  29 => 86,  27 => 85,  23 => 84,  26 => 25,  21 => 24,  155 => 93,  149 => 90,  146 => 124,  143 => 88,  137 => 85,  134 => 119,  131 => 83,  125 => 81,  122 => 80,  116 => 113,  113 => 112,  110 => 75,  104 => 73,  102 => 72,  99 => 71,  93 => 107,  90 => 67,  84 => 64,  81 => 63,  79 => 62,  76 => 61,  70 => 99,  67 => 57,  64 => 56,  58 => 96,  55 => 52,  52 => 51,  46 => 48,  43 => 92,  41 => 91,  36 => 45,  30 => 43,  28 => 42,  24 => 41,  19 => 82,);
    }
}
