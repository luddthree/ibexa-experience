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

/* @IbexaHttpCache/fields/content_fields.html.twig */
class __TwigTemplate_d2558148f88129ad87e2a4798b095336 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'ezobjectrelationlist_field' => [$this, 'block_ezobjectrelationlist_field'],
            'ezimageasset_field' => [$this, 'block_ezimageasset_field'],
            'ezobjectrelation_field' => [$this, 'block_ezobjectrelation_field'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 3
        return "@IbexaCore/content_fields.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaHttpCache/fields/content_fields.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaHttpCache/fields/content_fields.html.twig"));

        $this->parent = $this->loadTemplate("@IbexaCore/content_fields.html.twig", "@IbexaHttpCache/fields/content_fields.html.twig", 3);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 5
    public function block_ezobjectrelationlist_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelationlist_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelationlist_field"));

        // line 6
        yield "    ";
        $___internal_parse_4_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 7
            yield "        ";
            if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 7, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 7, $this->source); })()))) {
                // line 8
                yield "            <ul ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
                ";
                // line 9
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 9, $this->source); })()), "value", [], "any", false, false, false, 9), "destinationContentIds", [], "any", false, false, false, 9));
                foreach ($context['_seq'] as $context["_key"] => $context["contentId"]) {
                    // line 10
                    yield "                    ";
                    if (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 10, $this->source); })()), "available", [], "any", false, false, false, 10), $context["contentId"], [], "array", false, false, false, 10)) {
                        // line 11
                        yield "                        ";
                        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Ibexa\HttpCache\Twig\ContentTaggingExtension']->tagHttpCacheForRelationIds($context["contentId"]), "html", null, true);
                        yield "
                        <li>
                            ";
                        // line 13
                        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content:viewAction", ["contentId" => $context["contentId"], "viewType" => "embed", "layout" => false]));
                        yield "
                        </li>
                    ";
                    }
                    // line 16
                    yield "                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['contentId'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 17
                yield "            </ul>
        ";
            }
            // line 19
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 6
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_4_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 22
    public function block_ezimageasset_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        // line 23
        yield "    ";
        $___internal_parse_5_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 24
            yield "        ";
            if (( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 24, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 24, $this->source); })())) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 24, $this->source); })()), "available", [], "any", false, false, false, 24))) {
                // line 25
                yield "            ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Ibexa\HttpCache\Twig\ContentTaggingExtension']->tagHttpCacheForRelationIds(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 25, $this->source); })()), "value", [], "any", false, false, false, 25), "destinationContentId", [], "any", false, false, false, 25)), "html", null, true);
                yield "
            <div ";
                // line 26
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
                ";
                // line 27
                yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content:embedAction", ["contentId" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 28
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 28, $this->source); })()), "value", [], "any", false, false, false, 28), "destinationContentId", [], "any", false, false, false, 28), "viewType" => "asset_image", "no_layout" => true, "params" => ["parameters" => Twig\Extension\CoreExtension::merge(((                // line 32
array_key_exists("parameters", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 32, $this->source); })()), ["alias" => "original"])) : (["alias" => "original"])), ["alternativeText" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 32, $this->source); })()), "value", [], "any", false, false, false, 32), "alternativeText", [], "any", false, false, false, 32)])]]));
                // line 34
                yield "
            </div>
        ";
            }
            // line 37
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 23
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_5_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 40
    public function block_ezobjectrelation_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelation_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezobjectrelation_field"));

        // line 41
        yield "    ";
        $___internal_parse_6_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 42
            yield "        ";
            if (( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 42, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 42, $this->source); })())) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 42, $this->source); })()), "available", [], "any", false, false, false, 42))) {
                // line 43
                yield "            ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Ibexa\HttpCache\Twig\ContentTaggingExtension']->tagHttpCacheForRelationIds(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 43, $this->source); })()), "value", [], "any", false, false, false, 43), "destinationContentId", [], "any", false, false, false, 43)), "html", null, true);
                yield "
            <div ";
                // line 44
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
                ";
                // line 45
                yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("ibexa_content:viewAction", ["contentId" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 45, $this->source); })()), "value", [], "any", false, false, false, 45), "destinationContentId", [], "any", false, false, false, 45), "viewType" => "text_linked", "layout" => false]));
                yield "
            </div>
        ";
            }
            // line 48
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 41
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_6_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaHttpCache/fields/content_fields.html.twig";
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
        return array (  210 => 41,  206 => 48,  200 => 45,  196 => 44,  191 => 43,  188 => 42,  185 => 41,  175 => 40,  164 => 23,  160 => 37,  155 => 34,  153 => 32,  152 => 28,  151 => 27,  147 => 26,  142 => 25,  139 => 24,  136 => 23,  126 => 22,  115 => 6,  111 => 19,  107 => 17,  101 => 16,  95 => 13,  89 => 11,  86 => 10,  82 => 9,  77 => 8,  74 => 7,  71 => 6,  61 => 5,  38 => 3,);
    }

    public function getSourceContext()
    {
        return new Source("{% trans_default_domain 'ibexa_fields_groups' %}

{% extends \"@IbexaCore/content_fields.html.twig\" %}

{% block ezobjectrelationlist_field %}
    {% apply spaceless %}
        {% if not ibexa_field_is_empty( content, field ) %}
            <ul {{ block( 'field_attributes' ) }}>
                {% for contentId in field.value.destinationContentIds %}
                    {% if parameters.available[contentId] %}
                        {{ ibexa_http_cache_tag_relation_ids(contentId) }}
                        <li>
                            {{ render( controller( \"ibexa_content:viewAction\", {'contentId': contentId, 'viewType': 'embed', 'layout': false} ) ) }}
                        </li>
                    {% endif %}
                {% endfor %}
            </ul>
        {% endif %}
    {% endapply %}
{% endblock %}

{% block ezimageasset_field %}
    {% apply spaceless %}
        {% if not ibexa_field_is_empty(content, field) and parameters.available %}
            {{ ibexa_http_cache_tag_relation_ids(field.value.destinationContentId) }}
            <div {{ block('field_attributes') }}>
                {{ render(controller('ibexa_content:embedAction', {
                    contentId: field.value.destinationContentId,
                    viewType: 'asset_image',
                    no_layout: true,
                    params: {
                        parameters: parameters|default({'alias': 'original'})|merge({'alternativeText': field.value.alternativeText })
                    }
                }))}}
            </div>
        {% endif %}
    {% endapply %}
{% endblock %}

{% block ezobjectrelation_field %}
    {% apply spaceless %}
        {% if not ibexa_field_is_empty( content, field ) and parameters.available %}
            {{ ibexa_http_cache_tag_relation_ids(field.value.destinationContentId) }}
            <div {{ block( 'field_attributes' ) }}>
                {{ render( controller( \"ibexa_content:viewAction\", {'contentId': field.value.destinationContentId, 'viewType': 'text_linked', 'layout': false} ) ) }}
            </div>
        {% endif %}
    {% endapply %}
{% endblock %}
", "@IbexaHttpCache/fields/content_fields.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/http-cache/src/bundle/Resources/views/fields/content_fields.html.twig");
    }
}
