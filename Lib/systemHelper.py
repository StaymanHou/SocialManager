import platform

def specialize_path(gene_path):
	if platform.system() is 'Windows':
		return gene_path.replace('/','\\')
	return gene_path
