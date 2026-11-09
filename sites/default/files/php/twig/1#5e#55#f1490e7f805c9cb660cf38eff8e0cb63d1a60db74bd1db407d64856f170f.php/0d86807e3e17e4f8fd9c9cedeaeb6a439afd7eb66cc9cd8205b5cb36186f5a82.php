<?php

/* core/modules/system/templates/links.html.twig */
class __TwigTemplate_5e55f1490e7f805c9cb660cf38eff8e0cb63d1a60db74bd1db407d64856f170f extends Drupal\Core\Template\TwigTemplate
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
        // line 38
        if ((isset($context["links"]) ? $context["links"] : null)) {
            // line 39
            if ((isset($context["heading"]) ? $context["heading"] : null)) {
                // line 40
                if ($this->getAttribute((isset($context["heading"]) ? $context["heading"] : null), "level")) {
                    // line 41
                    echo "<";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "level"));
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "attributes"));
                    echo ">";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "text"));
                    echo "</";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "level"));
                    echo ">";
                } else {
                    // line 43
                    echo "<h2";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "attributes"));
                    echo ">";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "heading"), "text"));
                    echo "</h2>";
                }
            }
            // line 46
            echo "<ul";
            echo twig_render_var($this->getContextReference($context, "attributes"));
            echo ">";
            // line 47
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["links"]) ? $context["links"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 48
                echo "<li";
                echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "attributes"));
                echo ">";
                // line 49
                if ($this->getAttribute((isset($context["item"]) ? $context["item"] : null), "link")) {
                    // line 50
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "link"));
                } elseif ($this->getAttribute((isset($context["item"]) ? $context["item"] : null), "text_attributes")) {
                    // line 52
                    echo "<span";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "text_attributes"));
                    echo ">";
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "text"));
                    echo "</span>";
                } else {
                    // line 54
                    echo twig_render_var($this->getAttribute($this->getContextReference($context, "item"), "text"));
                }
                // line 56
                echo "</li>";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 58
            echo "</ul>";
        }
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/links.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 58,  70 => 56,  67 => 54,  60 => 52,  55 => 49,  21 => 39,  85 => 44,  79 => 43,  65 => 37,  50 => 32,  47 => 47,  28 => 27,  77 => 58,  71 => 39,  66 => 54,  63 => 36,  57 => 50,  54 => 33,  35 => 43,  26 => 41,  24 => 26,  51 => 48,  48 => 48,  46 => 47,  43 => 46,  41 => 46,  36 => 29,  34 => 23,  32 => 28,  25 => 41,  23 => 40,  19 => 38,);
    }
}
