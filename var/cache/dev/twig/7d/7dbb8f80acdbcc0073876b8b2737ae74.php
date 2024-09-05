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

/* @IbexaFieldTypeQuery/fieldtype/field_view.html.twig */
class __TwigTemplate_d0f2285b9089a47cb1efe606a18f4bba extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'ezcontentquery_field' => [$this, 'block_ezcontentquery_field'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "@IbexaCore/content_fields.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaFieldTypeQuery/fieldtype/field_view.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaFieldTypeQuery/fieldtype/field_view.html.twig"));

        $this->parent = $this->loadTemplate("@IbexaCore/content_fields.html.twig", "@IbexaFieldTypeQuery/fieldtype/field_view.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_ezcontentquery_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezcontentquery_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezcontentquery_field"));

        // line 4
        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content:viewAction", ["content" =>         // line 7
(isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 7, $this->source); })()), "location" => ((        // line 8
$context["location"]) ?? (null)), "queryFieldDefinitionIdentifier" => CoreExtension::getAttribute($this->env, $this->source,         // line 9
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 9, $this->source); })()), "fieldDefIdentifier", [], "any", false, false, false, 9), "viewType" => CoreExtension::getAttribute($this->env, $this->source,         // line 10
(isset($context["ezContentQueryViews"]) || array_key_exists("ezContentQueryViews", $context) ? $context["ezContentQueryViews"] : (function () { throw new RuntimeError('Variable "ezContentQueryViews" does not exist.', 10, $this->source); })()), "field", [], "array", false, false, false, 10), "itemViewType" => ((        // line 11
array_key_exists("itemViewType", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["itemViewType"]) || array_key_exists("itemViewType", $context) ? $context["itemViewType"] : (function () { throw new RuntimeError('Variable "itemViewType" does not exist.', 11, $this->source); })()), CoreExtension::getAttribute($this->env, $this->source, (isset($context["ezContentQueryViews"]) || array_key_exists("ezContentQueryViews", $context) ? $context["ezContentQueryViews"] : (function () { throw new RuntimeError('Variable "ezContentQueryViews" does not exist.', 11, $this->source); })()), "item", [], "array", false, false, false, 11))) : (CoreExtension::getAttribute($this->env, $this->source, (isset($context["ezContentQueryViews"]) || array_key_exists("ezContentQueryViews", $context) ? $context["ezContentQueryViews"] : (function () { throw new RuntimeError('Variable "ezContentQueryViews" does not exist.', 11, $this->source); })()), "item", [], "array", false, false, false, 11))), "enablePagination" => ((CoreExtension::getAttribute($this->env, $this->source,         // line 12
($context["parameters"] ?? null), "enablePagination", [], "any", true, true, false, 12)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "enablePagination", [], "any", false, false, false, 12), false)) : (false)), "disablePagination" => ((CoreExtension::getAttribute($this->env, $this->source,         // line 13
($context["parameters"] ?? null), "disablePagination", [], "any", true, true, false, 13)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "disablePagination", [], "any", false, false, false, 13), false)) : (false)), "itemsPerPage" => ((CoreExtension::getAttribute($this->env, $this->source,         // line 14
($context["parameters"] ?? null), "itemsPerPage", [], "any", true, true, false, 14)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["parameters"] ?? null), "itemsPerPage", [], "any", false, false, false, 14), false)) : (false))]));
        // line 16
        yield "
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
        return "@IbexaFieldTypeQuery/fieldtype/field_view.html.twig";
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
        return array (  79 => 16,  77 => 14,  76 => 13,  75 => 12,  74 => 11,  73 => 10,  72 => 9,  71 => 8,  70 => 7,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends '@IbexaCore/content_fields.html.twig' %}

{% block ezcontentquery_field %}
{{ render(controller(
    \"ibexa_content:viewAction\",
    {
        \"content\": content,
        \"location\": location ?? null,
        \"queryFieldDefinitionIdentifier\": field.fieldDefIdentifier,
        \"viewType\": ezContentQueryViews['field'],
        \"itemViewType\": itemViewType|default(ezContentQueryViews['item']),
        \"enablePagination\": parameters.enablePagination|default(false),
        \"disablePagination\": parameters.disablePagination|default(false),
        \"itemsPerPage\": parameters.itemsPerPage|default(false)
    }
)) }}
{% endblock %}
", "@IbexaFieldTypeQuery/fieldtype/field_view.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/fieldtype-query/src/bundle/Resources/views/fieldtype/field_view.html.twig");
    }
}
