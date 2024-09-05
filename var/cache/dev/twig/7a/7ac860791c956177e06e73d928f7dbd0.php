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

/* @ludvik/product_catalog/field_type/customer_group.html.twig */
class __TwigTemplate_6cec22e3637287bba5a0e1ce233bf0e2 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'ibexa_customer_group_field' => [$this, 'block_ibexa_customer_group_field'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/product_catalog/field_type/customer_group.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@ludvik/product_catalog/field_type/customer_group.html.twig"));

        // line 1
        yield from $this->unwrap()->yieldBlock('ibexa_customer_group_field', $context, $blocks);
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    public function block_ibexa_customer_group_field($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_customer_group_field"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "ibexa_customer_group_field"));

        // line 2
        yield "    ";
        $context["customer_group"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["parameters"]) || array_key_exists("parameters", $context) ? $context["parameters"] : (function () { throw new RuntimeError('Variable "parameters" does not exist.', 2, $this->source); })()), "customer_group", [], "any", false, false, false, 2);
        // line 3
        yield "    ";
        if ( !(null === (isset($context["customer_group"]) || array_key_exists("customer_group", $context) ? $context["customer_group"] : (function () { throw new RuntimeError('Variable "customer_group" does not exist.', 3, $this->source); })()))) {
            // line 4
            yield "        ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["customer_group"]) || array_key_exists("customer_group", $context) ? $context["customer_group"] : (function () { throw new RuntimeError('Variable "customer_group" does not exist.', 4, $this->source); })()), "name", [], "any", false, false, false, 4), "html", null, true);
            yield "
    ";
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
        return "@ludvik/product_catalog/field_type/customer_group.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  71 => 4,  68 => 3,  65 => 2,  45 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% block ibexa_customer_group_field %}
    {% set customer_group = parameters.customer_group %}
    {% if customer_group is not null %}
        {{ customer_group.name }}
    {% endif %}
{% endblock %}
", "@ludvik/product_catalog/field_type/customer_group.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/product-catalog/src/bundle/Resources/views/themes/standard/product_catalog/field_type/customer_group.html.twig");
    }
}
