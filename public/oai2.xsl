<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:oai="http://www.openarchives.org/OAI/2.0/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">
<xsl:output method="html"/>

<xsl:template match="/">
<html>
<head>
    <title>OAI-PMH 2.0 Repository</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; margin: 0; padding: 0; color: #333; font-size: 13px; line-height: 1.5; }
        .header { background-color: #007ab2; color: #fff; padding: 15px 20px; border-bottom: 1px solid #005f8b; }
        .header h1 { margin: 0; font-size: 20px; font-weight: normal; }
        .content { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        .verb-nav { margin-bottom: 20px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 2px; }
        .verb-nav a { margin-right: 15px; text-decoration: none; color: #007ab2; font-weight: bold; }
        .verb-nav a:hover { text-decoration: underline; }

        table { border-collapse: collapse; width: 100%; margin-top: 20px; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; color: #555; }
        
        /* Styles for ListIdentifiers Table */
        .identifier-link { color: #007ab2; font-weight: bold; text-decoration: none; }
        .identifier-link:hover { text-decoration: underline; }

        .label { width: 200px; font-weight: bold; color: #555; background: #fafafa; }
        
        .record { margin-bottom: 30px; border: 1px solid #ddd; border-radius: 3px; overflow: hidden; }
        .record-header { background: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #ddd; font-size: 12px; color: #666; }
        .record-header span { margin-right: 20px; }
        .record-body { padding: 15px; }

        table.metadata { border: none; }
        table.metadata td { border: none; padding: 4px 8px; }
        table.metadata td.meta-label { width: 150px; font-weight: bold; color: #555; text-align: right; border-right: 3px solid #eee; text-transform: capitalize; }
        
        a { color: #007ab2; text-decoration: none; }
        .xml-note { margin-top: 40px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OAI-PMH 2.0 Repository</h1>
    </div>
    
    <div class="content">
        <div class="verb-nav">
            <xsl:variable name="base" select="//oai:request" />
            <a href="{$base}?verb=Identify">Identify</a>
            <a href="{$base}?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a>
            <a href="{$base}?verb=ListMetadataFormats">ListMetadataFormats</a>
            <a href="{$base}?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Request Verb:</strong> <xsl:value-of select="//oai:request/@verb"/><br/>
            <strong>Response Date:</strong> <xsl:value-of select="//oai:responseDate"/>
        </div>

        <xsl:for-each select="//oai:Identify">
            <h2>Repository Identification</h2>
            <table>
                <tr><td class="label">Repository Name</td><td><xsl:value-of select="oai:repositoryName"/></td></tr>
                <tr><td class="label">Base URL</td><td><xsl:value-of select="oai:baseURL"/></td></tr>
                <tr><td class="label">Protocol Version</td><td><xsl:value-of select="oai:protocolVersion"/></td></tr>
                <tr><td class="label">Admin Email</td><td><xsl:value-of select="oai:adminEmail"/></td></tr>
            </table>
        </xsl:for-each>

        <xsl:if test="//oai:ListMetadataFormats">
            <h2>List Metadata Formats</h2>
            <table>
                <thead>
                    <tr><th>Metadata Prefix</th><th>Schema</th><th>Metadata Namespace</th></tr>
                </thead>
                <tbody>
                    <xsl:for-each select="//oai:metadataFormat">
                    <tr>
                        <td style="font-weight:bold; color:#007ab2;"><xsl:value-of select="oai:metadataPrefix"/></td>
                        <td><a href="{oai:schema}"><xsl:value-of select="oai:schema"/></a></td>
                        <td><a href="{oai:metadataNamespace}"><xsl:value-of select="oai:metadataNamespace"/></a></td>
                    </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </xsl:if>

        <xsl:if test="//oai:ListIdentifiers">
            <h2>List Identifiers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Identifier</th>
                        <th>Datestamp</th>
                        <th>SetSpec</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:for-each select="//oai:header">
                    <tr>
                        <td>
                            <xsl:variable name="id" select="oai:identifier"/>
                            <span class="identifier-link"><xsl:value-of select="oai:identifier"/></span>
                        </td>
                        <td><xsl:value-of select="oai:datestamp"/></td>
                        <td><xsl:value-of select="oai:setSpec"/></td>
                    </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </xsl:if>

        <xsl:if test="//oai:ListRecords">
            <h2>List Records</h2>
            <xsl:for-each select="//oai:record">
                <div class="record">
                    <div class="record-header">
                        <span><strong>Identifier:</strong> <xsl:value-of select="oai:header/oai:identifier"/></span>
                        <span><strong>Datestamp:</strong> <xsl:value-of select="oai:header/oai:datestamp"/></span>
                    </div>
                    <div class="record-body">
                        <table class="metadata">
                            <xsl:for-each select="oai:metadata/oai_dc:dc/*">
                                <tr>
                                    <td class="meta-label"><xsl:value-of select="local-name()"/></td>
                                    <td><xsl:value-of select="."/></td>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </div>
                </div>
            </xsl:for-each>
        </xsl:if>

        <xsl:if test="//oai:error">
            <div style="padding: 20px; background: #fff5f5; border: 1px solid #fc8181; color: #c53030;">
                <h3>OAI Error: <xsl:value-of select="//oai:error/@code"/></h3>
                <p><xsl:value-of select="//oai:error"/></p>
            </div>
        </xsl:if>

        <div class="xml-note">IAMJOS OAI-PMH Interface</div>
    </div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
