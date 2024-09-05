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

/* @ludvik/ui/field_type/preview/ezdam.html.twig */
class __TwigTemplate_822f3ef6c080b89fa8b7477aabd4ed5b extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ezimageasset_field' => [$this, 'block_ezimageasset_field'],
            'field_attributes' => [$this, 'block_field_attributes'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/ui/field_type/preview/ezdam.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/ui/field_type/preview/ezdam.html.twig"));

        // line 2
        yield "
";
        // line 3
        yield from $this->unwrap()->yieldBlock('ezimageasset_field', $context, $blocks);
        // line 25
        yield "
";
        // line 26
        yield from $this->unwrap()->yieldBlock('field_attributes', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    // line 3
    public function block_ezimageasset_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezimageasset_field"));

        // line 4
        yield "    ";
        $___internal_parse_2_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 5
            yield "        ";
            if (( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 5, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 5, $this->source); })())) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 5, $this->source); })()), "available", [], "any", false, false, false, 5))) {
                // line 6
                yield "            <div ";
                yield from                 $this->unwrap()->yieldBlock("field_attributes", $context, $blocks);
                yield ">
                ";
                // line 7
                yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("Ibexa\\Bundle\\Connector\\Dam\\Controller\\AssetViewController::viewAction", ["destinationContentId" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 9
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 9, $this->source); })()), "value", [], "any", false, false, false, 9), "destinationContentId", [], "any", false, false, false, 9), "assetSource" => (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 10
($context["field"] ?? null), "value", [], "any", false, true, false, 10), "source", [], "any", true, true, false, 10) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 10), "source", [], "any", false, false, false, 10)))) ? (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["field"] ?? null), "value", [], "any", false, true, false, 10), "source", [], "any", false, false, false, 10)) : (null)), "viewType" => "asset_image", "no_layout" => true, "params" => ["parameters" => Twig\Extension\CoreExtension::merge(((                // line 14
array_key_exists("parameters", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 14, $this->source); })()), [])) : ([])), ["alternativeText" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 15
(isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 15, $this->source); })()), "value", [], "any", false, false, false, 15), "alternativeText", [], "any", false, false, false, 15), "transformation" => ((                // line 16
$context["transformation"]) ?? (null))])]]));
                // line 20
                yield "
            </div>
        ";
            }
            // line 23
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 4
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_2_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    // line 26
    public function block_field_attributes($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "field_attributes"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "field_attributes"));

        // line 27
        yield "    ";
        $___internal_parse_3_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 28
            yield "        ";
            $context["attr"] = ((array_key_exists("attr", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 28, $this->source); })()), [])) : ([]));
            // line 29
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["attr"]) || array_key_exists("attr", $context) ? $context["attr"] : (function () { throw new RuntimeError('Variable "attr" does not exist.', 29, $this->source); })()));
            foreach ($context['_seq'] as $context["attrname"] => $context["attrvalue"]) {
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrname"], "html", null, true);
                yield "=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["attrvalue"], "html", null, true);
                yield "\" ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            yield "    ";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 27
        yield Twig\Extension\CoreExtension::spaceless($___internal_parse_3_);
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/ui/field_type/preview/ezdam.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  146 => 27,  142 => 30,  129 => 29,  126 => 28,  123 => 27,  113 => 26,  102 => 4,  98 => 23,  93 => 20,  91 => 16,  90 => 15,  89 => 14,  88 => 10,  87 => 9,  86 => 7,  81 => 6,  78 => 5,  75 => 4,  65 => 3,  54 => 26,  51 => 25,  49 => 3,  46 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{% trans_default_domain 'ibexa_content_fields' %}

{% block ezimageasset_field %}
    {% apply spaceless %}
        {% if not ibexa_field_is_empty(content, field) and parameters.available %}
            <div {{ block('field_attributes') }}>
                {{ render(controller(
                    'Ibexa\\\\Bundle\\\\Connector\\\\Dam\\\\Controller\\\\AssetViewController::viewAction', {
                        destinationContentId: field.value.destinationContentId,
                        assetSource: field.value.source ?? null,
                        viewType: 'asset_image',
                        no_layout: true,
                        params: {
                            parameters: parameters|default({})|merge({
                                alternativeText: field.value.alternativeText,
                                transformation: transformation ?? null
                            })
                        }
                    }
                )) }}
            </div>
        {% endif %}
    {% endapply %}
{% endblock %}

{% block field_attributes %}
    {% apply spaceless %}
        {% set attr = attr|default( {} ) %}
        {% for attrname, attrvalue in attr %}{{ attrname }}=\"{{ attrvalue }}\" {% endfor %}
    {% endapply %}
{% endblock %}
", "@ludvik/ui/field_type/preview/ezdam.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/connector-dam/src/bundle/Resources/views/themes/standard/ui/field_type/preview/ezdam.html.twig");
    }
}
