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

/* @ludvik/ibexa/taxonomy/fieldtypes.html.twig */
class __TwigTemplate_4d11d9267b4be154da583215912b5d46 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ibexa_taxonomy_entry_field' => [$this, 'block_ibexa_taxonomy_entry_field'],
            'ibexa_taxonomy_entry_assignment_field' => [$this, 'block_ibexa_taxonomy_entry_assignment_field'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/ibexa/taxonomy/fieldtypes.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/ibexa/taxonomy/fieldtypes.html.twig"));

        // line 1
        yield from $this->unwrap()->yieldBlock('ibexa_taxonomy_entry_field', $context, $blocks);
        // line 8
        yield "
";
        // line 9
        yield from $this->unwrap()->yieldBlock('ibexa_taxonomy_entry_assignment_field', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    // line 1
    public function block_ibexa_taxonomy_entry_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_taxonomy_entry_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_taxonomy_entry_field"));

        // line 2
        yield "    ";
        $___internal_parse_0_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 3
            yield "        ";
            if ( !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 3, $this->source); })()), "value", [], "any", false, false, false, 3), "taxonomyEntry", [], "any", false, false, false, 3))) {
                // line 4
                yield "            ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 4, $this->source); })()), "value", [], "any", false, false, false, 4), "taxonomyEntry", [], "any", false, false, false, 4), "name", [], "any", false, false, false, 4), "html", null, true);
                yield "
        ";
            }
            // line 6
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 2
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_0_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 9
    public function block_ibexa_taxonomy_entry_assignment_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_taxonomy_entry_assignment_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_taxonomy_entry_assignment_field"));

        // line 10
        yield "    ";
        $___internal_parse_1_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 11
            yield "        ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::join(Twig\Extension\CoreExtension::map($this->env, ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 11), "getTaxonomyEntries", [], "method", true, true, false, 11)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 11), "getTaxonomyEntries", [], "method", false, false, false, 11), [])) : ([])), function ($__taxonomyEntry__) use ($context, $macros) { $context["taxonomyEntry"] = $__taxonomyEntry__; return ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["taxonomyEntry"] ?? null), "names", [], "any", false, true, false, 11), CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 11, $this->source); })()), "languageCode", [], "any", false, false, false, 11), [], "array", true, true, false, 11)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["taxonomyEntry"] ?? null), "names", [], "any", false, true, false, 11), CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 11, $this->source); })()), "languageCode", [], "any", false, false, false, 11), [], "array", false, false, false, 11), CoreExtension::getAttribute($this->env, $this->source, (isset($context["taxonomyEntry"]) || array_key_exists("taxonomyEntry", $context) ? $context["taxonomyEntry"] : (function () { throw new RuntimeError('Variable "taxonomyEntry" does not exist.', 11, $this->source); })()), "name", [], "any", false, false, false, 11))) : (CoreExtension::getAttribute($this->env, $this->source, (isset($context["taxonomyEntry"]) || array_key_exists("taxonomyEntry", $context) ? $context["taxonomyEntry"] : (function () { throw new RuntimeError('Variable "taxonomyEntry" does not exist.', 11, $this->source); })()), "name", [], "any", false, false, false, 11))); }), ", "), "html", null, true);
            yield "
    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 10
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_1_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/ibexa/taxonomy/fieldtypes.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  119 => 10,  112 => 11,  109 => 10,  99 => 9,  88 => 2,  84 => 6,  78 => 4,  75 => 3,  72 => 2,  62 => 1,  51 => 9,  48 => 8,  46 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% block ibexa_taxonomy_entry_field %}
    {% apply spaceless %}
        {% if field.value.taxonomyEntry is not null %}
            {{ field.value.taxonomyEntry.name }}
        {% endif %}
    {% endapply %}
{% endblock %}

{% block ibexa_taxonomy_entry_assignment_field %}
    {% apply spaceless %}
        {{ field.value.getTaxonomyEntries()|default([])|map(taxonomyEntry => taxonomyEntry.names[field.languageCode]|default(taxonomyEntry.name))|join(', ') }}
    {% endapply %}
{% endblock %}
", "@ludvik/ibexa/taxonomy/fieldtypes.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/taxonomy/src/bundle/Resources/views/themes/standard/ibexa/taxonomy/fieldtypes.html.twig");
    }
}
