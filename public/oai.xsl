<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:oai="http://www.openarchives.org/OAI/2.0/" 
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:toolkit="http://oai.dlib.vt.edu/OAI/metadata/toolkit"
    xmlns:oai-identifier="http://www.openarchives.org/OAI/2.0/oai-identifier"
    exclude-result-prefixes="oai oai_dc dc toolkit oai-identifier">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
<head>
    <title>OAI 2.0 Request Results</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 16px; margin: 20px; color: #000; }
        h1 { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        h2 { font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; }
        h3 { font-size: 14px; font-weight: bold; margin: 10px 0 5px 0; }
        .nav-links { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; font-size: 14px; }
        .nav-links a { margin-right: 10px; color: #0000CC; text-decoration: underline; }
        .label-cell { background-color: #e0e0ff; font-weight: bold; text-align: right; padding: 3px 10px; width: 150px; vertical-align: top; white-space: nowrap; }
        .value-cell { background-color: #fff; padding: 3px 5px; vertical-align: top; }
        .dc-label { background-color: #ffffcc; font-weight: bold; text-align: right; padding: 3px 10px; width: 180px; vertical-align: top; color: #000; }
        table.info-table { border-collapse: separate; border-spacing: 2px; margin-bottom: 10px; font-size: 14px; width: 100%; }
        .xml-box { background-color: #ffffcc; border: 1px solid #ccc; padding: 15px; font-family: monospace; font-size: 12px; margin-top: 10px; white-space: pre-wrap; }
        .mini-btn { background-color: #e0e0ff; font-size: 12px; padding: 1px 4px; margin-left: 5px; color: #000; text-decoration: none; border: 1px solid #aaa; }
        .record-header-bar { background-color: #e0e0ff; padding: 5px 10px; font-weight: bold; border-bottom: 1px solid #ccc; margin-top: 20px; }
        .oai-record { border: 1px solid #ccc; margin-bottom: 30px; }
        .oai-record-content { padding: 10px; }
    </style>
</head>
<body>
    <h1>OAI 2.0 Request Results</h1>

    <div class="nav-links">
        <a href="?verb=Identify">Identify</a> | 
        <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a> | 
        <a href="?verb=ListSets">ListSets</a> | 
        <a href="?verb=ListMetadataFormats">ListMetadataFormats</a> | 
        <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
    </div>

    <table class="info-table" style="width: auto;">
        <tr><td class="label-cell">Datestamp of response</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:responseDate"/></td></tr>
        <tr><td class="label-cell">Request URL</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:request"/></td></tr>
    </table>

    <xsl:if test="oai:OAI-PMH/oai:error">
        <h2 style="color:red">OAI Error: <xsl:value-of select="oai:OAI-PMH/oai:error/@code"/></h2>
        <p><xsl:value-of select="oai:OAI-PMH/oai:error"/></p>
    </xsl:if>

    <xsl:if test="oai:OAI-PMH/oai:Identify">
        <h3>Repository Identification</h3>
        <table class="info-table" style="width: auto;">
            <tr><td class="label-cell">Repository Name</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:repositoryName"/></td></tr>
            <tr><td class="label-cell">Base URL</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:baseURL"/></td></tr>
            <tr><td class="label-cell">Protocol Version</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:protocolVersion"/></td></tr>
            <tr><td class="label-cell">Earliest Datestamp</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:earliestDatestamp"/></td></tr>
            <tr><td class="label-cell">Admin Email</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:adminEmail"/></td></tr>
        </table>
        
        <xsl:if test="oai:OAI-PMH/oai:Identify/oai:description/oai-identifier:*">
            <h3>OAI-Identifier</h3>
            <table class="info-table" style="width: auto;">
                <tr><td class="label-cell">Scheme</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/oai-identifier:oai-identifier/oai-identifier:scheme"/></td></tr>
                <tr><td class="label-cell">Repository Identifier</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/oai-identifier:oai-identifier/oai-identifier:repositoryIdentifier"/></td></tr>
                <tr><td class="label-cell">Delimiter</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/oai-identifier:oai-identifier/oai-identifier:delimiter"/></td></tr>
                <tr><td class="label-cell">Sample Identifier</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/oai-identifier:oai-identifier/oai-identifier:sampleIdentifier"/></td></tr>
            </table>
        </xsl:if>

        <xsl:if test="oai:OAI-PMH/oai:Identify/oai:description/toolkit:*">
            <h3>About the XSLT</h3>
            <table class="info-table" style="width: auto;">
                <tr><td class="label-cell">Title</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:title"/></td></tr>
                <tr><td class="label-cell">Version</td><td class="value-cell"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:version"/></td></tr>
                <tr><td class="label-cell">URL</td><td class="value-cell"><a href="{oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:URL}"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:URL"/></a></td></tr>
                <tr><td class="label-cell">Author</td><td class="value-cell">
                    <xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:author/toolkit:name"/> 
                    (&lt;<a href="mailto:{oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:author/toolkit:email}"><xsl:value-of select="oai:OAI-PMH/oai:Identify/oai:description/toolkit:toolkit/toolkit:author/toolkit:email"/></a>&gt;)
                </td></tr>
            </table>
        </xsl:if>
    </xsl:if>

    <xsl:if test="oai:OAI-PMH/oai:ListSets">
        <p>Request was of type ListSets.</p>
        <xsl:for-each select="oai:OAI-PMH/oai:ListSets/oai:set">
            <h2>Set</h2>
            <table class="info-table" style="width: auto;">
                <tr><td class="label-cell">setName</td><td class="value-cell"><xsl:value-of select="oai:setName"/></td></tr>
                <tr>
                    <td class="label-cell">setSpec</td>
                    <td class="value-cell">
                        <xsl:value-of select="oai:setSpec"/>
                        <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="mini-btn">Identifiers</a>
                        <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={oai:setSpec}" class="mini-btn">Records</a>
                    </td>
                </tr>
            </table>
        </xsl:for-each>
    </xsl:if>

    <xsl:if test="oai:OAI-PMH/oai:ListMetadataFormats">
        <p>Request was of type ListMetadataFormats.</p>
        <xsl:for-each select="oai:OAI-PMH/oai:ListMetadataFormats/oai:metadataFormat">
             <table class="info-table" style="width: auto;">
                <tr><td class="label-cell">metadataPrefix</td><td class="value-cell"><xsl:value-of select="oai:metadataPrefix"/></td></tr>
                <tr><td class="label-cell">schema</td><td class="value-cell"><a href="{oai:schema}"><xsl:value-of select="oai:schema"/></a></td></tr>
                <tr><td class="label-cell">metadataNamespace</td><td class="value-cell"><a href="{oai:metadataNamespace}"><xsl:value-of select="oai:metadataNamespace"/></a></td></tr>
             </table>
             <hr/>
        </xsl:for-each>
    </xsl:if>

    <xsl:if test="oai:OAI-PMH/oai:ListIdentifiers">
        <p>Request was of type ListIdentifiers.</p>
        <xsl:for-each select="oai:OAI-PMH/oai:ListIdentifiers/oai:header">
            <h2>OAI Record Header</h2>
            <table class="info-table" style="width: auto;">
                <tr>
                    <td class="label-cell">OAI Identifier</td>
                    <td class="value-cell">
                        <xsl:value-of select="oai:identifier"/>
                        <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:identifier}" class="mini-btn">oai_dc</a>
                        <a href="?verb=ListMetadataFormats&amp;identifier={oai:identifier}" class="mini-btn">formats</a>
                    </td>
                </tr>
                <tr><td class="label-cell">Datestamp</td><td class="value-cell"><xsl:value-of select="oai:datestamp"/></td></tr>
                <tr>
                    <td class="label-cell">setSpec</td>
                    <td class="value-cell"><xsl:value-of select="oai:setSpec"/></td>
                </tr>
            </table>
        </xsl:for-each>
    </xsl:if>

    <!-- Handle ListRecords and GetRecord (Same structure) -->
    <xsl:if test="oai:OAI-PMH/oai:ListRecords or oai:OAI-PMH/oai:GetRecord">
        <p>Request was of type <xsl:value-of select="local-name(oai:OAI-PMH/*[local-name()='ListRecords' or local-name()='GetRecord'])"/>.</p>
        
        <!-- Select records from either ListRecords or GetRecord -->
        <xsl:for-each select="oai:OAI-PMH/oai:ListRecords/oai:record | oai:OAI-PMH/oai:GetRecord/oai:record">
            <div class="oai-record">
                <div class="record-header-bar">
                    OAI Record: <xsl:value-of select="oai:header/oai:identifier"/>
                </div>
                <div class="oai-record-content">
                    
                    <!-- Header -->
                    <h3>OAI Record Header</h3>
                    <table class="info-table" style="width: auto;">
                        <tr>
                            <td class="label-cell">OAI Identifier</td>
                            <td class="value-cell">
                                <xsl:value-of select="oai:header/oai:identifier"/>
                                <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:header/oai:identifier}" class="mini-btn">oai_dc</a>
                                <a href="?verb=ListMetadataFormats&amp;identifier={oai:header/oai:identifier}" class="mini-btn">formats</a>
                            </td>
                        </tr>
                        <tr><td class="label-cell">Datestamp</td><td class="value-cell"><xsl:value-of select="oai:header/oai:datestamp"/></td></tr>
                        <tr><td class="label-cell">setSpec</td><td class="value-cell"><xsl:value-of select="oai:header/oai:setSpec"/></td></tr>
                    </table>

                    <!-- Metadata (oai_dc) -->
                    <xsl:if test="oai:metadata/oai_dc:dc">
                        <h3>Dublin Core Metadata (oai_dc)</h3>
                        <table class="info-table">
                            <xsl:for-each select="oai:metadata/oai_dc:dc/*">
                                <tr>
                                    <td class="dc-label">
                                        <!-- Beautify local name if possible, otherwise just use name -->
                                        <xsl:value-of select="local-name()"/>
                                    </td>
                                    <td class="value-cell">
                                        <!-- Handle links if content starts with http -->
                                        <xsl:choose>
                                            <xsl:when test="starts-with(., 'http')">
                                                <a href="{.}"><xsl:value-of select="."/></a>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:value-of select="."/>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </xsl:if>
                </div>
            </div>
        </xsl:for-each>
    </xsl:if>

    <div style="margin-top: 20px; font-size: 12px; color: gray; border-top:1px solid #ccc; padding-top:10px;">
        IAMJOS OAI-PMH System (XSLT Rendered)
    </div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
