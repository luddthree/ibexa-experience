Feature: Set up Site Factory

    @admin
  Scenario Outline: Create additional languages and configure them
    Given Language "<languageName>" with code "<languageCode>" exists
    And I append configuration to "admin_group" siteaccess
      | key                          | value          |
      | languages                    | <languageCode> |

    Examples:
      | languageName | languageCode |
      | English      | eng-GB       |
      | Polski       | pol-PL       |
      | French       | fre-FR       |
      | German       | ger-DE       |
      | Italian      | ita-IT       |
      | Spanish      | esl-ES       |
      | Portuguese   | por-PT       |
      | Ukrainian    | ukr-UA       |
      | Swedish      | swe-SE       |
      | Norwegian    | nor-NO       |
      | Finnish      | fin-FI       |
      | Danish       | dan-DK       |
      | Hungarian    | hun-HU       |
      | Japanese     | jpn-JP       |
      | Korean       | kor-KR       |
      | Croatian     | cro-HR       |
      | Turkish      | tur-TR       |
      | Russian      | rus-RU       |
      | Serbian      | ser-SR       |


  @admin
  Scenario: Create Site Skeleton structure
    Given I create "folder" Content items in "Site-skeletons" in "eng-GB"
      | name           | short_name     |
      | MySiteSkeleton | MySiteSkeleton |
    And I create "folder" Content items in "Site-skeletons/MySiteSkeleton" in "eng-GB"
      | name      | short_name |
      | Item1     | Item1      |
      | Item2     | Item2      |
      | Item3     | Item3      |
    And I create "folder" Content items in "Site-skeletons/MySiteSkeleton/Item1" in "eng-GB"
      | name           | short_name     |
      | ChildOfItem1_1 | ChildOfItem1_1 |
      | ChildOfItem1_2 | ChildOfItem1_2 |
    And I create "folder" Content items in "Site-skeletons/MySiteSkeleton/Item2" in "eng-GB"
      | name           | short_name     |
      | ChildOfItem2_1 | ChildOfItem2_1 |
      | ChildOfItem2_2 | ChildOfItem2_2 |

  @admin
  Scenario Outline: Translate Site Skeleton items
    Given I edit "Site skeletons/MySiteSkeleton" Content item in "<languageCode>"
      | name                   | short_name             |
      | Skeleton<languageName> | Skeleton<languageName> |
    And I edit "Site skeletons/MySiteSkeleton/Item1/ChildOfItem1_1" Content item in "<languageCode>"
      | name                 | short_name           |
      | Child1<languageName> | Child1<languageName> |

      Examples:
        | languageName | languageCode |
        | Polski       | pol-PL       |
        | French       | fre-FR       |
        | English (United Kingdom) | eng-GB       |
        | German       | ger-DE       |
        | Italian      | ita-IT       |
        | Spanish      | esl-ES       |
        | Portuguese   | por-PT       |
        | Ukrainian    | ukr-UA       |
        | Swedish      | swe-SE       |
        | Norwegian    | nor-NO       |
        | Finnish      | fin-FI       |
        | Danish       | dan-DK       |
        | Hungarian    | hun-HU       |
        | Japanese     | jpn-JP       |
        | Korean       | kor-KR       |
        | Croatian     | cro-HR       |
        | Turkish      | tur-TR       |
        | Russian      | rus-RU       |
        | Serbian      | ser-SR       |

  @admin
  Scenario: Configure Site Factory
    Given I set configuration to "ibexa_site_factory" in "config/packages/ibexa_site_factory.yaml"
    """
        enabled: true
        update_roles: [Anonymous, Administrator]
    """
    And I append configuration to "ibexa.siteaccess.match"
    """
      Compound\LogicalAnd:
        test:
            matchers:
                Map\URI:
                    test: true
                Map\Host:
                    test.example.com: true
            match: test
      '@Ibexa\SiteFactory\SiteAccessMatcher': ~
    """

  Scenario: Create Landing Page template
    Given I create a file "templates/themes/standard/full/landing_page.html.twig" with contents
    """
    {% extends "@ibexadesign/pagelayout.html.twig" %}
    {% block test %}
        <div class="col-md-12">
            {{ ibexa_render_field(content, 'page') }}
        </div>
    {% endblock %}
    """

  @admin
  Scenario Outline: Configure Site Factory siteaccesses
    Given I append configuration to "ibexa_site_factory.templates" in "config/packages/ibexa_site_factory.yaml"
    """
          <template_id>:
            siteaccess_group: <sa_group>
            name: <template_name>
            thumbnail: https://picsum.photos/200/300
            site_skeleton_id: "%location_id(Site-skeletons/MySiteSkeleton)%"
    """
    And I append configuration to "ibexa.siteaccess.groups"
    """
          <sa_group>: []
    """
    And I set configuration to "<sa_group>" siteaccess
      | key                                                         | value                                 |
      | design                                                      | <template_id>                         |
      | pagelayout                                                  | @ibexadesign/pagelayout.html.twig        |
      | content_view.full.landing_page.template                     | @ibexadesign/full/landing_page.html.twig |
      | content_view.full.landing_page.match.Identifier\ContentType | landing_page                          |                              
    And I create a file "templates/themes/<theme_name>/pagelayout.html.twig" with content from "Templates/SiteFactory/<layout>.html.twig"
    And I append configuration to "ibexa_design_engine.design_list" in "config/packages/ibexa_site_factory.yaml"
    """
        <template_id>: [<theme_name>]
    """

    Examples:
      | sa_group   | template_id | template_name | theme_name | layout      |
      | sf_group_1 | template1   | Template1     | sf_theme1  | pagelayout1 |
      | sf_group_2 | template2   | Template2     | sf_theme2  | pagelayout2 |
      | sf_group_3 | template3   | Template3     | sf_theme3  | pagelayout3 |
      | sf_group_4 | template4   | Template4     | sf_theme4  | pagelayout4 |
      | sf_group_5 | template5   | Template5     | sf_theme5  | pagelayout5 |
      | sf_group_6 | template6   | Template6     | sf_theme6  | pagelayout6 |
      | sf_group_7 | template7   | Template7     | sf_theme7  | pagelayout7 |
      | sf_group_8 | template8   | Template8     | sf_theme8  | pagelayout8 |
      | sf_group_9 | template9   | Template9     | sf_theme9  | pagelayout9 |
      | sf_group_10| template10  | Template10    | sf_theme10 | pagelayout10|
      | sf_group_11| template11  | Template11    | sf_theme11 | pagelayout11|
      | sf_group_12| template12  | Template12    | sf_theme12 | pagelayout12|
      | sf_group_13| template13  | Template13    | sf_theme13 | pagelayout13|
      | sf_group_14| template14  | Template14    | sf_theme14 | pagelayout14|
      | sf_group_15| template15  | Template15    | sf_theme15 | pagelayout15|
      | sf_group_16| template16  | Template16    | sf_theme16 | pagelayout16|
      | sf_group_17| template17  | Template17    | sf_theme17 | pagelayout17|
      | sf_group_18| template18  | Template18    | sf_theme18 | pagelayout18|
      | sf_group_19| template19  | Template19    | sf_theme19 | pagelayout19|
      | sf_group_20| template20  | Template20    | sf_theme20 | pagelayout20|
