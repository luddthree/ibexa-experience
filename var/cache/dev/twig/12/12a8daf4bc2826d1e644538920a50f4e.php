<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @ludvik/full/blog.html.twig */
class __TwigTemplate_25c247c931d54fd6950b94d09310bede extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 2
        return "@ibexadesign/pagelayout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/full/blog.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/full/blog.html.twig"));

        $this->parent = $this->loadTemplate("@ibexadesign/pagelayout.html.twig", "@ludvik/full/blog.html.twig", 2);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 4
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        // line 5
        yield "    <article>
        <h1>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 6, $this->source); })()), "name", [], "any", false, false, false, 6), "html", null, true);
        yield "</h1>
    ";
        // line 8
        yield "
        <div class=\"blog-content\">
<p>Blog fra Barcelona</p>
";
        // line 11
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["children"]) || array_key_exists("children", $context) ? $context["children"] : (function () { throw new RuntimeError('Variable "children" does not exist.', 11, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 12
            yield "    ";
            yield $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RenderExtension']->render(CoreExtension::getAttribute($this->env, $this->source, $context["item"], "valueObject", [], "any", false, false, false, 12));
            yield "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        yield "        </div>
    </article>
    ";
        // line 16
        yield "


    <style>
/* General styles for the article containing the blog overview */
article {
    background-color: #fff; /* White background for the content */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    margin: 20px auto; /* Center the article with margin */
    padding: 20px; /* Padding inside the article */
    max-width: 800px; /* Maximum width of the article */
}

/* Header styles within the article for the blog title */
article h1 {
    color: #333; /* Dark grey color for the text */
    border-bottom: 2px solid #007BFF; /* Blue bottom border for the title */
    padding-bottom: 10px; /* Space below the title before the border */
    margin-bottom: 20px; /* Space below the border */
}

/* Styles for the blog content div that contains individual posts */
.blog-content {
    font-family: 'Arial', sans-serif; /* Consistent font family */
    color: #666; /* Lighter text color for readability */
    line-height: 1.6; /* Increased line height for readability */
}

/* Specific styles for each blog post rendered in the loop */
.blog-content > div {
    margin-top: 15px; /* Space between blog post entries */
    padding: 10px; /* Padding inside each blog post entry */
    border-top: 1px solid #eee; /* Subtle top border for each post */
}

/* Responsive design adjustments for smaller screens */
@media (max-width: 768px) {
    article {
        padding: 10px;
        margin: 10px;
    }
}

</style>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/full/blog.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  98 => 16,  94 => 13,  85 => 12,  81 => 11,  76 => 8,  72 => 6,  69 => 5,  59 => 4,  36 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# templates/full/blog.html.twig #}
{% extends \"@ibexadesign/pagelayout.html.twig\" %}

{% block content %}
    <article>
        <h1>{{ content.name }}</h1>
    {# {{ ibexa_render_field(content, 'content') }} #}

        <div class=\"blog-content\">
<p>Blog fra Barcelona</p>
{% for item in children %}
    {{ ibexa_render(item.valueObject) }}
{% endfor %}        </div>
    </article>
    {# {{dump()}} #}



    <style>
/* General styles for the article containing the blog overview */
article {
    background-color: #fff; /* White background for the content */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    margin: 20px auto; /* Center the article with margin */
    padding: 20px; /* Padding inside the article */
    max-width: 800px; /* Maximum width of the article */
}

/* Header styles within the article for the blog title */
article h1 {
    color: #333; /* Dark grey color for the text */
    border-bottom: 2px solid #007BFF; /* Blue bottom border for the title */
    padding-bottom: 10px; /* Space below the title before the border */
    margin-bottom: 20px; /* Space below the border */
}

/* Styles for the blog content div that contains individual posts */
.blog-content {
    font-family: 'Arial', sans-serif; /* Consistent font family */
    color: #666; /* Lighter text color for readability */
    line-height: 1.6; /* Increased line height for readability */
}

/* Specific styles for each blog post rendered in the loop */
.blog-content > div {
    margin-top: 15px; /* Space between blog post entries */
    padding: 10px; /* Padding inside each blog post entry */
    border-top: 1px solid #eee; /* Subtle top border for each post */
}

/* Responsive design adjustments for smaller screens */
@media (max-width: 768px) {
    article {
        padding: 10px;
        margin: 10px;
    }
}

</style>
{% endblock %}



", "@ludvik/full/blog.html.twig", "/var/www/ibexa/blog/ibexa-experience/templates/themes/ludvik/full/blog.html.twig");
    }
}
