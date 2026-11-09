<?php

/* themes/flatui/templates/dashboard/dashboard.html.twig */
class __TwigTemplate_4af580a7a614adc1db85d52ed5a88426d7ab399dc16de82189737793cbebe335 extends Drupal\Core\Template\TwigTemplate
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
        echo "<p>Welcome to Trial dashboard !!</p>

<script type=\"text/template\" id=\"widget-view\">
    <li class=\"<%= class_name %>\" data-row=\"<%=widget_row%>\" data-col=\"<%=widget_col%>\" data-sizex=\"<%=widget_sizex%>\" data-sizey=\"<%=widget_sizey%>\">
        <header class=\"clearfix gridster-heading\">
            <ul class=\"unstyled\">
                <!--<li class=\"btn\"><i class=\"icon-large icon-tags\"></i></li>-->
                <li><h5><%= widget_title %></h5></li>
            </ul>
        </header>
        <article class=\"clearfix gridster-content2\"><%= widget_content %></div>
        <aside class=\"clearfix gridster-bottom\">
            <span class=\"pull-left gridster-move\"><i class=\"icon-large icon-move\"></i></span>
        </aside>
    </li>
</script>";
    }

    public function getTemplateName()
    {
        return "themes/flatui/templates/dashboard/dashboard.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
