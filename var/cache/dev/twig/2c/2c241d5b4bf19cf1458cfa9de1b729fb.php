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

/* @ludvik/fields/ezform_field.html.twig */
class __TwigTemplate_ffb2464c07d1c06b29f27a4c94aea5ea extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'ezform_field' => [$this, 'block_ezform_field'],
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/fields/ezform_field.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/fields/ezform_field.html.twig"));

        $this->parent = $this->loadTemplate("@IbexaCore/content_fields.html.twig", "@ludvik/fields/ezform_field.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_ezform_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezform_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ezform_field"));

        // line 4
        yield "    ";
        $context["formValue"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 4, $this->source); })()), "value", [], "any", false, false, false, 4), "getForm", [], "method", false, false, false, 4);
        // line 5
        yield "    ";
        if ((isset($context["formValue"]) || array_key_exists("formValue", $context) ? $context["formValue"] : (function () { throw new RuntimeError('Variable "formValue" does not exist.', 5, $this->source); })())) {
            // line 6
            yield "        ";
            $context["form"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["formValue"]) || array_key_exists("formValue", $context) ? $context["formValue"] : (function () { throw new RuntimeError('Variable "formValue" does not exist.', 6, $this->source); })()), "createView", [], "method", false, false, false, 6);
            // line 7
            yield "
        ";
            // line 8
            $this->env->getRuntime("Symfony\\Component\\Form\\FormRenderer")->setTheme((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 8, $this->source); })()), ["bootstrap_4_layout.html.twig"], true);
            // line 9
            yield "
        ";
            // line 10
            $___internal_parse_36_ = ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
                // line 11
                yield "            ";
                if ( !$this->extensions['Ibexa\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension']->isFieldEmpty((isset($context["content"]) || array_key_exists("content", $context) ? $context["content"] : (function () { throw new RuntimeError('Variable "content" does not exist.', 11, $this->source); })()), (isset($context["field"]) || array_key_exists("field", $context) ? $context["field"] : (function () { throw new RuntimeError('Variable "field" does not exist.', 11, $this->source); })()))) {
                    // line 12
                    yield "                ";
                    yield                     $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["form"]) || array_key_exists("form", $context) ? $context["form"] : (function () { throw new RuntimeError('Variable "form" does not exist.', 12, $this->source); })()), 'form');
                    yield "
            ";
                }
                // line 14
                yield "        ";
                return; yield '';
            })())) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 10
            yield Twig\Extension\CoreExtension::spaceless($___internal_parse_36_);
            // line 15
            yield "    ";
        }
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@ludvik/fields/ezform_field.html.twig";
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
        return array (  103 => 15,  101 => 10,  97 => 14,  91 => 12,  88 => 11,  86 => 10,  83 => 9,  81 => 8,  78 => 7,  75 => 6,  72 => 5,  69 => 4,  59 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"@IbexaCore/content_fields.html.twig\" %}

{% block ezform_field %}
    {% set formValue = field.value.getForm() %}
    {% if formValue %}
        {% set form = formValue.createView() %}

        {% form_theme form 'bootstrap_4_layout.html.twig' %}

        {% apply spaceless %}
            {% if not ibexa_field_is_empty(content, field) %}
                {{ form(form) }}
            {% endif %}
        {% endapply %}
    {% endif %}
{% endblock %}
", "@ludvik/fields/ezform_field.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/form-builder/src/bundle/Resources/views/themes/standard/fields/ezform_field.html.twig");
    }
}
