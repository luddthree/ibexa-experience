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

/* @IbexaDebug/Profiler/siteaccess/toolbar.html.twig */
class __TwigTemplate_a5e6cd68622c373c861f90fbf6670c6f extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/siteaccess/toolbar.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/siteaccess/toolbar.html.twig"));

        // line 1
        if ( !(null === CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 1, $this->source); })()), "siteAccess", [], "any", false, false, false, 1))) {
            // line 2
            yield "<div class=\"sf-toolbar-info-piece\">
    <b>Site Access</b>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>name</b> <span class=\"sf-toolbar-status sf-toolbar-status-green\">";
            // line 6
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 6, $this->source); })()), "siteAccess", [], "any", false, false, false, 6), "name", [], "any", false, false, false, 6), "html", null, true);
            yield "</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>matching type</b> <span class=\"sf-toolbar-status sf-toolbar-status-green\">";
            // line 9
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 9, $this->source); })()), "siteAccess", [], "any", false, false, false, 9), "matchingType", [], "any", false, false, false, 9), "html", null, true);
            yield "</span>
</div>
";
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
        return "@IbexaDebug/Profiler/siteaccess/toolbar.html.twig";
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
        return array (  58 => 9,  52 => 6,  46 => 2,  44 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% if collector.siteAccess is not null %}
<div class=\"sf-toolbar-info-piece\">
    <b>Site Access</b>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>name</b> <span class=\"sf-toolbar-status sf-toolbar-status-green\">{{ collector.siteAccess.name }}</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>matching type</b> <span class=\"sf-toolbar-status sf-toolbar-status-green\">{{ collector.siteAccess.matchingType }}</span>
</div>
{% endif %}
", "@IbexaDebug/Profiler/siteaccess/toolbar.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/core/src/bundle/Debug/Resources/views/Profiler/siteaccess/toolbar.html.twig");
    }
}
