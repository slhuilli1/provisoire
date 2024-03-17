<?php
	defined('_JEXEC') or die('Access deny');
	
	class plgContentProvisoire extends JPlugin 
	{
		function onContentPrepare($content, $article, $params, $limit){	
			
			
			//Je commence par virer tout ce qui est en commentaire 
			$re = '/<!--.*-->/m';
			
			
			$subst = "";
			$result = preg_replace($re, $subst, $article->text);
			
			//Ma regex pour capturer les éléments provisoires
			$re = '/<li.*data-provisoire="(.*)">(.*)<\/a>.*\/li>/m';
			
			$query = "SELECT * FROM #__content where  state=1";
			$db = JFactory::getDBO();
			$db->setQuery($query); 
			$articles = $db->loadObjectList(); 
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins/content/provisoire/style.css');
			$ch='';
			$ch = '<div class="liste-documents-provisoires"><ul>';
			
			foreach($articles as $unarticle){
				if (strpos($unarticle->introtext,'data-provisoire')>0)
				{
					$re = '/<a.*data\-provisoire.*=.*"(.*)"/m';
					preg_match_all($re, $unarticle->introtext, $matches, PREG_SET_ORDER, 0);
						
					foreach($matches as $unDoc)
					{
						
						$re = '/href="(.*)".*>(.*)<\/a>.*ref-interne-seo">(.*)<\/span>/mU';
						preg_match_all($re, $unDoc[0], $matchesD, PREG_SET_ORDER, 0);
						
						
						//provisoirité
						$re = '/data-provisoire.*=.*"(.*)"/mU';
						preg_match_all($re, $unDoc[0], $provisoires, PREG_SET_ORDER, 0);

						array_push($matchesD[0],$provisoires[0][1]);
						$t = explode('-',$matchesD[0][4]);
						$ch .= '<li>Le document <span class="nom-document-provisoire">'.$matchesD[0][2].' </span><span class="ref-document-provisoire">'.$matchesD[0][3].'</span>  de l\'article <span class="id-article-document-provisoire">'.$article->id.'</span> a été déclaré provisoire le <span class="date-declaration-document-provisoire">'.$t[2]."-".$t[1]."-".$t[0].'</span></li>';
						
					}
					
				
				}
			}
			
			$ch .= "</div>";
			$article->text = str_replace('{fichiersprovisoires}', $ch, $article->text);
			
		}
	}