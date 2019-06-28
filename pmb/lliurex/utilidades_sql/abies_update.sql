
-- Se añade la sección Indeterminada para la importacion Abies

Insert into docs_section (idsection,section_libelle,sdoc_codage_import,sdoc_owner,section_pic,section_visible_opac) select '28','General','','0','','1' from dual where NOT EXISTS(Select * from docs_section where idsection like '28' and section_libelle like 'Indeterminado');

-- Se limpia la correspondencia entre sección y localización para sólo incluir la biblioteca

delete from docsloc_section where num_section !='28' and num_location !='1';

-- Se actuliza el campo flag_pret para los estado de Abies

update docs_statut set pret_flag='1' where idstatut='101' or idstatut='103';
update docs_statut set statut_allow_resa='1' where idstatut='101';
update docs_statut set pret_flag='0' where idstatut='102';
