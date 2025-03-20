#!/bin/bash

# 输入序列文件和数据库
input_sequence="$1"
database="$2"
output_file="$3"

# 运行BLAST进行比对
blastp -query $input_sequence -db $database -out $output_file -evalue 1e-5 -outfmt 6

echo "BLAST序列比对已完成，结果保存在 $output_file"
