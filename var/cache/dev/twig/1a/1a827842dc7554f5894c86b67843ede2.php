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

/* @IbexaDebug/Profiler/persistence/toolbar.html.twig */
class __TwigTemplate_5316f5b13ead01472d3481835a4c52ed extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/persistence/toolbar.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@IbexaDebug/Profiler/persistence/toolbar.html.twig"));

        // line 1
        $context["stats"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 1, $this->source); })()), "stats", [], "any", false, false, false, 1);
        // line 3
        $context["status_cache"] = (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 3, $this->source); })()), "miss", [], "any", false, false, false, 3) > 100)) ? ("red") : ((((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 3, $this->source); })()), "hit", [], "any", false, false, false, 3) > 100)) ? ("yellow") : (((((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 3, $this->source); })()), "miss", [], "any", false, false, false, 3) < 1) && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 3, $this->source); })()), "hit", [], "any", false, false, false, 3) < 20))) ? ("green") : (""))))));
        // line 4
        yield "
<div class=\"sf-toolbar-info-piece\">
    <b>SPI Persistence/Cache</b>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>uncached calls</b> <span class=\"sf-toolbar-status sf-toolbar-status-";
        // line 9
        yield (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 9, $this->source); })()), "uncached", [], "any", false, false, false, 9) > 10)) ? ("yellow") : ((((CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 9, $this->source); })()), "uncached", [], "any", false, false, false, 9) < 2)) ? ("green") : (""))));
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 9, $this->source); })()), "uncached", [], "any", false, false, false, 9), "html", null, true);
        yield "</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>misses / hits / memory</b> <span class=\"sf-toolbar-status sf-toolbar-status-";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["status_cache"]) || array_key_exists("status_cache", $context) ? $context["status_cache"] : (function () { throw new RuntimeError('Variable "status_cache" does not exist.', 12, $this->source); })()), "html", null, true);
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 12, $this->source); })()), "miss", [], "any", false, false, false, 12), "html", null, true);
        yield " / ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 12, $this->source); })()), "hit", [], "any", false, false, false, 12), "html", null, true);
        yield " / ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 12, $this->source); })()), "memory", [], "any", false, false, false, 12), "html", null, true);
        yield "</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>uncached handlers</b> <span class=\"sf-toolbar-status sf-toolbar-status-";
        // line 15
        yield (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 15, $this->source); })()), "handlerscount", [], "any", false, false, false, 15) > 1)) ? ("") : ("green"));
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new RuntimeError('Variable "collector" does not exist.', 15, $this->source); })()), "handlerscount", [], "any", false, false, false, 15), "html", null, true);
        yield "</span>
</div>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@IbexaDebug/Profiler/persistence/toolbar.html.twig";
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
        return array (  75 => 15,  63 => 12,  55 => 9,  48 => 4,  46 => 3,  44 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% set stats = collector.stats %}
{# Set cache status to green  if misses is below 1 and total hits is below 20 (with remote Redis 20 cache lookups can take 50-100ms) #}
{% set status_cache = stats.miss > 100 ? 'red' : (stats.hit > 100 ? 'yellow' : (stats.miss < 1  and stats.hit < 20 ? 'green' : '')) %}

<div class=\"sf-toolbar-info-piece\">
    <b>SPI Persistence/Cache</b>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>uncached calls</b> <span class=\"sf-toolbar-status sf-toolbar-status-{{ stats.uncached > 10 ? 'yellow' : (stats.uncached < 2 ? 'green' : '') }}\">{{ stats.uncached }}</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>misses / hits / memory</b> <span class=\"sf-toolbar-status sf-toolbar-status-{{ status_cache }}\">{{ stats.miss }} / {{ stats.hit }} / {{ stats.memory }}</span>
</div>
<div class=\"sf-toolbar-info-piece\">
    <b>uncached handlers</b> <span class=\"sf-toolbar-status sf-toolbar-status-{{ collector.handlerscount > 1 ? '' : 'green' }}\">{{ collector.handlerscount }}</span>
</div>
", "@IbexaDebug/Profiler/persistence/toolbar.html.twig", "/var/www/ibexa/blog/ibexa-experience/vendor/ibexa/core/src/bundle/Debug/Resources/views/Profiler/persistence/toolbar.html.twig");
    }
}
