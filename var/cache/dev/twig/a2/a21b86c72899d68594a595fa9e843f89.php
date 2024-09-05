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

/* @IbexaCore/default/content/embed.html.twig */
class __TwigTemplate_c431e0979bcbc23ec1469ffd4f769e01 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaCore/default/content/embed.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaCore/default/content/embed.html.twig"));

        // line 1
        $context["content_name"] = $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->getTranslatedContentName((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 1, $this->source); })()));
        // line 2
        yield "
";
        // line 3
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["objectParameters"] ?? null), "doNotGenerateEmbedUrl", [], "any", true, true, false, 3) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["objectParameters"]) || array_key_exists("objectParameters", $context) ? $context["objectParameters"] : (function () { throw new RuntimeError('Variable "objectParameters" does not exist.', 3, $this->source); })()), "doNotGenerateEmbedUrl", [], "any", false, false, false, 3))) {
            // line 4
            yield "    <h3>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 4, $this->source); })()), "name", [], "any", false, false, false, 4), "html", null, true);
            yield "</h3>
";
        } else {
            // line 6
            yield "    ";
            if (array_key_exists("location", $context)) {
                // line 7
                yield "        <h3><a href=\"";
                yield $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getPath((isset($context["location"]) || array_key_exists("location", $context) ? $context["location"] : (function () { throw new RuntimeError('Variable "location" does not exist.', 7, $this->source); })()));
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["content_name"]) || array_key_exists("content_name", $context) ? $context["content_name"] : (function () { throw new RuntimeError('Variable "content_name" does not exist.', 7, $this->source); })()), "html", null, true);
                yield "</a></h3>
    ";
            } elseif ( !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,             // line 8
(isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 8, $this->source); })()), "contentInfo", [], "any", false, false, false, 8), "mainLocationId", [], "any", false, false, false, 8))) {
                // line 9
                yield "        <h3><a href=\"";
                yield $this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\RoutingExtension']->getPath((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 9, $this->source); })()));
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["content_name"]) || array_key_exists("content_name", $context) ? $context["content_name"] : (function () { throw new RuntimeError('Variable "content_name" does not exist.', 9, $this->source); })()), "html", null, true);
                yield "</a></h3>
    ";
            } else {
                // line 11
                yield "        <h3>";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["content_name"]) || array_key_exists("content_name", $context) ? $context["content_name"] : (function () { throw new RuntimeError('Variable "content_name" does not exist.', 11, $this->source); })()), "html", null, true);
                yield "</h3>
    ";
            }
        }
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaCore/default/content/embed.html.twig";
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
        return array (  77 => 11,  69 => 9,  67 => 8,  60 => 7,  57 => 6,  51 => 4,  49 => 3,  46 => 2,  44 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set content_name=ibexa_content_name(content) %}

{% if objectParameters.doNotGenerateEmbedUrl is defined and objectParameters.doNotGenerateEmbedUrl %}
    <h3>{{ content.name }}</h3>
{% else %}
    {% if location is defined %}
        <h3><a href=\"{{ ibexa_path(location) }}\">{{ content_name }}</a></h3>
    {% elseif content.contentInfo.mainLocationId is not null %}
        <h3><a href=\"{{ ibexa_path(content) }}\">{{ content_name }}</a></h3>
    {% else %}
        <h3>{{ content_name }}</h3>
    {% endif %}
{% endif %}
", "@IbexaCore/default/content/embed.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/core/src/bundle/Core/Resources/views/default/content/embed.html.twig");
    }
}
